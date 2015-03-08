<?php

/**
 * @copyright	Copyright (C) 2005 - 2011 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

// Added this to load the language in the front end
JFactory::getLanguage()->load('plg_user_domainrestriction');

class plgUserDomainRestriction extends JPlugin {

    public $_tlds;
    public $_domains;
    public $_emails;
    public $_badtlds;
    public $_baddomains;
    public $_bademails;
    public $_email;
    public $_domain;
    public $_tld;
    public $_allowed;
    public $_gmp;
    protected $app;

    public function __construct(&$subject, $config = array()) {
        $this->_gmp = function_exists('gmp_pow');
        $this->app = JFactory::getApplication();
        parent::__construct($subject, $config);
    }

    public function onUserBeforeSave($user, $isnew, $new) {
        // are we manipulating a new user from the site?
        if ($this->app->isAdmin()) return true;
        if($isnew) {
            $listtest = $this->_blackwhite($this->_getIP());
            if($listtest === true) {
                return true;
            }
            if($listtest !== false) {
                $this->app->enqueueMessage(JText::_('PLG_USER_DOMAINRESTRICTION_DENY'),'warning');
                return false;
            }
        } else {
            if ($this->params->get('ignorechange',1)) return true;
        }

        $result = true;

        // retrieve and clean up domain and email params
        $this->_sortParams();
        $this->_parseEmail($new['email']);

        $this->_allowed = ($this->_tlds || $this->_domains || $this->_emails) ?
                ($this->_decision(true) ? true : false) : true;

        if ($this->_allowed &&
                ($this->_badtlds || $this->_baddomains || $this->_bademails)
        )
            $this->_allowed = $this->_decision(); // disallowed entries

        if (!$this->_allowed) {
            $this->_emailFailedAttempt($new); // send an email with the tried details
            JFactory::getLanguage()->load('plg_user_domainrestriction', JPATH_ADMINISTRATOR);
            $message = $isnew?'PLG_USER_DOMAINRESTRICTION_DENY':'PLG_USER_DOMAINRESTRICTION_DENYCHANGE';
            $this->app->enqueueMessage(JText::_($message),'warning');
            $result = false;
        }

        return $result;
    }

    public function onUserAfterSave($user, $isnew, $success, $msg) {
        if ($isnew)
            $this->_updateGroups($user);
        return true;
    }

    public function onUserLogin($user, $options) {
        $this->_updateGroups($user);
        return true;
    }

    private function _getIP() {
        $ip = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : (
            getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : (
                getenv('HTTP_X_FORWARDED') ? getenv('HTTP_X_FORWARDED') : (
                    getenv('HTTP_FORWARDED_FOR') ? getenv('HTTP_FORWARDED_FOR') : (
                        getenv('HTTP_FORWARDED') ? getenv('HTTP_FORWARDED') : (
                            getenv('REMOTE_ADDR')
                        )
                    )
                )
            )
        );
        return $ip;
    }

    private function _emailFailedAttempt($user) {
        $shouldSendEmail = ($this->params->get('sendemailonfailure') == "1");
        $emailRecipient = $this->params->get('emailforfailures');

        if($shouldSendEmail && $emailRecipient != "") {
            // we are not allowed here, so send a message to membership@inquire.org.uk
            $mailer = JFactory::getMailer();
            $config = JFactory::getConfig();
            $sender = array(
                'webmaster@inquire.org.uk',
                'Inquire Website'
            );

            $mailer->setSender($sender);

            $recipient = 'weavermjw@googlemail.com';
            $mailer->addRecipient($recipient);

            $body  = "A new user tried to register at www.inquire.org.uk, but their email address did not match the current whitelist.\n\n";
            $body .= "The user's details were as follows:\n\n";
            $body .= "Full Name: " . $user['name'] . "\n";
            $body .= "First Name: " . $user['cf_firstname'] . "\n";
            $body .= "Last Name: " . $user['cf_lastname'] . "\n";
            $body .= "Email address: " . $user['email'] . "\n";
            $body .= "Member Type: " . $user['cf_member_type'] . "\n";
            $body .= "Date: " . $user['registerDate'] . "\n\n";
            $body .= "Please consider this user's request to join the website.";

            $mailer->setSubject('Inquire Membership - User not on whitelist');
            $mailer->setBody($body);
            $send = $mailer->Send();
        }
    }

    private function _blackwhite($ip) {
        $whitelistnet = array();
        $blacklistnet = array();
        $whitelist = array();
        $blacklist = array();
        foreach (array('whitelist', 'blacklist') as $list) {
            $listnet = $list . 'net';
            $listdefault = ($list == 'blacklist') ? JText::_('PLG_USER_DOMAINRESTRICTION_DEFAULT_BLACKLIST') : JText::_('PLG_USER_DOMAINRESTRICTION_DEFAULT_WHITELIST');
            $$list = explode("\n", trim(str_replace(array("\r", "\t", " "), array('', '', ''), $this->params->get($list, $listdefault))));
            foreach ($$list as $key => $item) {
                $item = trim($item);
                if (preg_match('/\//', $item)) {
                    unset(${$list}[$key]);
                    array_push($$listnet, $item);
                } else {
                    ${$list}[$key] = $item;
                }
            }
        }
        if (in_array($ip, $whitelist)) {
            return true;
        }
        if (in_array($ip, $blacklist)) {
            return $ip;
        }
        if (count(array_merge($whitelistnet, $blacklistnet))) {
            $requires = array('IPv6Net.class.php'=>'IPv6Net','simplecidr.class.php'=>'SimpleCIDR');
            $require = $this->_gmp ? 'IPv6Net.class.php' : 'simplecidr.class.php';
            if(!class_exists($requires[$require])) require_once($require);
            foreach ($whitelistnet as $net) {
                $ipnet = $this->_bwnet($net);
                if ($ipnet->contains($ip)) {
                    return true;
                }
            }
            foreach ($blacklistnet as $net) {
                $ipnet = $this->_bwnet($net);
                if ($ipnet->contains($ip)) {
                    return $net;
                }
            }
        }
        return false;
    }

    private function _bwnet($net) {
        return $this->_gmp ? (new IPv6Net($net)) : SimpleCIDR::getInstance($net);
    }

    private function _decision($allowed = false) {
        $ret = $allowed ?
                $this->_mailmatch(array('_tlds', '_domains', '_emails')) :
                !$this->_mailmatch(array('_badtlds', '_baddomains', '_bademails'));
        return $ret;
    }

    private function _mailmatch($keys = array()) {
        $ret = false;
        if ($this->$keys[0] || $this->$keys[1] || $this->$keys[2])
            if (in_array($this->_tld, $this->$keys[0]) || in_array($this->_domain, $this->$keys[1]) || in_array($this->_email, $this->$keys[2]))
                $ret = true;
        return $ret;
    }

    private function _sortParams() {
        foreach (array('_tld', '_domain', '_email', '_badtld', '_baddomain', '_bademail') as $param) {
            $paramvalue = $this->params->get(str_replace('_', '', $param), null);
            $arrayvar = $param . 's';
            $this->$arrayvar = array();
            $this->$arrayvar = json_decode(strtolower(base64_decode($paramvalue)));
            foreach ($this->$arrayvar as $key => $d)
                if (!strlen(trim($d)))
                    unset($this->$arrayvar[$key]);
        }
    }

    private function _parseEmail($email) {
        $this->_email = strtolower($email);
        $email = explode('@', strtolower($email));
        $this->_domain = $email[1];
        $this->_tld = $this->_get_tld_from_url($email[1]);
        return $email[0];
    }

    private function _updateGroups($user) {
        // exit if in /administrator
        if ($this->app->isAdmin())
            return true;

        // exit if there aren't any autogroups
        $assignments = $this->_get_assignments();
        if (!$assignments)
            return true;

        $user = $this->_getUser($user['username']);

        $excludegroups = $this->params->get('excludegroup', array());
        foreach ($user->groups as $group)
            if (in_array($group, (array) $excludegroups))
                return true;

        $emailuser = $this->_parseEmail($user->email);
        $excluded = json_decode(str_replace('*',$emailuser,base64_decode($this->params->get('excludeauto', 'W10K'))));

        if (count($excluded) && in_array(strtolower($user->email), $excluded))
            return true;

        $akey = $this->_get_assignments_key($assignments);

        if ($akey) {
            $groupchange = false;
            foreach ($assignments[$akey] as $groupid) {
                if (!in_array($groupid, $user->groups)) {
                    JUserHelper::addUserToGroup($user->id, $groupid);
                    $groupchange = true;
                }
            }
            foreach ($user->groups as $groupid) {
                if (!in_array($groupid, $assignments[$akey])) {
                    JUserHelper::removeUserFromGroup($user->id, $groupid);
                    $groupchange = true;
                }
            }
            if ($groupchange) {
                $user->set('groups', JAccess::getGroupsByUser($user->id));
                $user->set('authlevels', JAccess::getAuthorisedViewLevels($user->id));
            }
        }
        return true;
    }

    function _getUser($username) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__users')->where('username = ' . $db->quote($username));
        $db->setQuery($query);
        $userid = $db->loadResult();
        $user = JFactory::getUser($userid);
        return $user;
    }

    function _get_assignments() {
        $assignments = json_decode(base64_decode($this->params->get('autogroups', 'W10K')));
        if (count($assignments)) {
            foreach ($assignments as $key => $assignment) {
                if (is_array($assignment)) {
                    $assignments[strtolower($assignment[0])] = $assignment[1];
                } else {
                    $assignments[strtolower($assignment->domain)] = $assignment->groups;
                }
                unset($assignments[$key]);
            }
        } else {
            $assignments = false;
        }
        return $assignments;
    }

    function _get_assignments_key($assignments) {
        return array_key_exists($this->_email, $assignments) ? $this->_email : (
                array_key_exists($this->_domain, $assignments) ? $this->_domain : (
                        array_key_exists($this->_tld, $assignments) ? $this->_tld : false
                        )
                );
    }

    function _get_tld_from_url($url) {
        $url = strpos($url, '://') ? $url : 'http://' . $url;
        $host = parse_url($url);
        $domain = str_replace("__", "", $host['host']);
        $tail = strlen($domain) >= 7 ? substr($domain, -7) : $domain;
        $tld = strstr($tail, ".");
        return preg_replace('/^\./', '', $tld);
    }

}

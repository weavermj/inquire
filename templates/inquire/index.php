<?php defined( '_JEXEC' ) or die;

// variables
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$active = $app->getMenu()->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/'.$this->template;

// generator tag
$this->setGenerator(null);

// Foundation CSS

$doc->addStyleSheet($tpath.'/lib/foundation/css/normalize.css');
$doc->addStyleSheet($tpath.'/lib/foundation/css/foundation.css');
$doc->addStyleSheet($tpath.'/lib/foundation-icons/foundation-icons.css');

// Founcation Scripts
$doc->addScript($tpath.'/lib/foundation/js/vendor/modernizr.js');

// template css
$doc->addStyleSheet($tpath.'/css/normalize.css');
$doc->addStyleSheet($tpath.'/css/main.css');

?><!doctype html>

<html lang="<?php echo $this->language; ?>">

<head>
  <jdoc:include type="head" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/images/apple-touch-icon-57x57-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $tpath; ?>/images/apple-touch-icon-72x72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $tpath; ?>/images/apple-touch-icon-114x114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $tpath; ?>/images/apple-touch-icon-144x144-precomposed.png">
</head>

<body class="<?php echo (($menu->getActive() == $menu->getDefault()) ? ('front') : ('site')).' '.$active->alias.' '.$pageclass; ?>">

<div class="contain-to-grid sticky">
  <nav class="top-bar" data-topbar data-options="sticky_on: large">
    <ul class="title-area">
        <li class="name">
          <h1><a href="#">INQUIRE</a></h1>
        </li>
       <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
      </ul>
    <section class="top-bar-section">
      <!-- Right Nav Section -->
    <!-- 	<ul class="right">
        <li class="active"><a href="#">Right Button Active</a></li>
        <li class="has-dropdown">
          <a href="#">Right Button Dropdown</a>
          <ul class="dropdown">
            <li><a href="#">First link in dropdown</a></li>
          </ul>
        </li>
      </ul> -->
      <!-- Left Nav Section -->
      <ul class="left">
        <li class="has-dropdown">
              <a href="#">About</a>
              <ul class="dropdown">
                  <li><a href="#">What we do</a></li>
                  <li><a href="#">Council</a></li>
                  <li><a href="#">Sponsors</a></li>
                  <li><a href="#">Contact Us</a></li>
              </ul>
          </li>
          <li class="has-dropdown">
              <a href="#">Membership</a>
              <ul class="dropdown">
                  <li><a href="#">How to join</a></li>
                  <li><a href="#">Benefits</a></li>
              </ul>
          </li>
          <li class="has-dropdown">
              <a href="#">Seminars</a>
              <ul class="dropdown">
                  <li><a href="#">Upcoming Seminars</a></li>
                  <li><a href="#">Attendance Policy</a></li>
                  <li><a href="#">Call for Papers</a></li>
                  <li><a href="#">Prize Winners</a></li>
              </ul>
          </li>
          <li class="has-dropdown">
              <a href="#">Research</a>
              <ul class="dropdown">
                  <li><a href="#">Completed Projects</a></li>
                  <li><a href="#">Call for Proposals</a></li>
              </ul>
          </li>

           <li class="has-dropdown">
              <a href="#">Resources</a>
              <ul class="dropdown">
                  <li><a href="#">Seminar Archive</a></li>
                  <li><a href="#">LinkedIn</a></li>
                  <li><a href="#">Useful Links</a></li>
              </ul>
          </li>

        <li class="hide-for-medium-up"><a href="#">Login</a></li>

      </ul>
    </section>
  </nav>
</div>


<jdoc:include type="modules" name="menu" style="none" />

  <!--
    YOUR CODE HERE
  -->
  <jdoc:include type="modules" name="debug" />

  <script src="<?php echo $tpath; ?>/lib/foundation/js/foundation.min.js"></script>
</body>

</html>

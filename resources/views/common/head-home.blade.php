<title>{{(isset($meta_title))?$meta_title:''}}</title>

<meta name="description" content="{{(isset($meta_description))?$meta_description:''}}"/>
<!-- <meta name="keywords" content="{{(isset($meta_keyword))?$meta_keyword:''}}"/> -->

<link rel="profile" href="http://gmpg.org/xfn/11">
<meta charset="UTF-8">
<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" /> -->
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>

<meta name="google-site-verification" content="RMiblwQEJJI_FmtKEPHC2gPesKOHeeS4Au8Z2qA_Yzc" />

<meta name="google-site-verification" content="MbEv4e7VIPEziscDStt5doHg4oJ9CcRH1HwGE0F9TN0" />


<link rel="icon" type="image/ico" href="{{url('favicon.ico')}}" />
<link rel="shortcut icon" href="{{url('favicon.ico')}}"/>
<link rel="stylesheet" type="text/css" href="{{url('css/bootstrap.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{url('css/style.css')}}?v={{rand(1,9)}}" />
<link rel="stylesheet" type="text/css" href="{{url('css/owl.carousel.min.css')}}" />
<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false && stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false){ ?>
<!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" /> -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet" /> 
<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet" />


<link rel="canonical" href="{{url()->current()}}" />


<!-- Open Graph data -->
<meta property="og:title" content="<?php echo (isset($meta_title))?$meta_title:''; ?>"/>
<meta property="og:type" content="article" />
<meta property="og:url" content="{{url()->current()}}" />
<meta property="og:image" content="<?php echo (isset($og_image))?$og_image:'https://www.johnpride.in/images/logo01.png'; ?>" alt="JohnPride"/>
<meta property="og:description" content="<?php echo (isset($meta_description))?$meta_description:'';?>"/>
<meta property="og:site_name" content="johnpride" />
<meta property="fb:app_id" content="465270991303584" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<!-- <meta name="twitter:card" content="article"> -->
<meta name="twitter:site" content="@johnpride">
<meta name="twitter:title" content="<?php echo (isset($meta_title))?$meta_title:''; ?>">
<meta name="twitter:description" content="<?php echo (isset($meta_description))?$meta_description:'';?>">
<meta name="twitter:creator" content="johnpride">
<meta name="twitter:image" content="<?php echo (isset($og_image))?$og_image:'https://www.johnpride.in/images/logo01.png';?>" alt="JohnPride">

<!-- <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,900;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">  -->
<!-- Facebook Pixel Code -->

<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '490313451709144');
fbq('track', 'PageView');
</script>

<!-- End Facebook Pixel Code -->

<!-- Global site tag (gtag.js) - Google Analytics -->

<?php } ?>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5KP42GB');</script>
<!-- End Google Tag Manager -->


<style>
.link_click_menu{ font-weight:600;    display: block;
    margin-bottom: 15px;   font-size: 20px !important;}
.link_click_menu:after {     display: block;
    content: '';
    width: 50px;
    height: 2px;
    background: #a77736;
    margin: 5px 0 6px 0; }
.sub-sub-menu-child { margin-left:16px; margin-bottom:5px; }
.sub-sub-menu > li {position: relative;  }
.sub-sub-menu-head { font-size: 17px !important;     min-width: 120px;
    display: inline-block; padding-right:10px;  font-weight: 600 !important;}
.topmenu { position:static;}
.topmenu>ul>li>ul { width:100%;}
.col_sec {     position: relative;
    margin: 0;
    padding-bottom: 0;
    list-style: none;
    width: 25%;
    background: 0 0;
    float: left;
    padding: 10px;
    min-height: 330px;}
	.sub-sub-menu li { padding:5px 0;}
	.sub-sub-menu-child > li a::before {
    content: "–";
}
.sub-sub-menu-child > li a { font-style: italic;}
/* .col_sec .sub-sub-menu-child:nth-child(1) { background:#000;} */
.ddclick-desktop  {
	position: absolute;
    right: auto;
    top: 0px;
    width: 32px;
    height: 32px;
    z-index: 999;
    background: #f1f1f1;
    cursor: pointer;
    color: #405464;
}
.ddclick-desktop:before {
	content: "\+";
    display: inline-block;
    width: 100%;
    text-align: center;
    line-height: 32px;
    font-size: 30px;
    height: 38px;
    width: 30px;
	font-weight:200;
}

.ddclick-desktop.active:before {content: "\-";  }
</style>

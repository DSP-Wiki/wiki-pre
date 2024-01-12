<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wgSitename = "DSP Wiki";
$wgMetaNamespace = "DSP_Wiki";

$wgScriptPath = "";
$wgScriptExtension = ".php";
#$wgArticlePath = "{$wgScript}/$1";
$wgUsePathInfo = true;

$actions = array( 'edit', 'watch', 'unwatch', 'delete','revert', 'rollback',
  'protect', 'unprotect', 'markpatrolled', 'render', 'submit', 'history', 'purge', 'info' );

foreach ( $actions as $action ) {
  $wgActionPaths[$action] = "/$1/$action";
}
$wgActionPaths['view'] = "/$1";
$wgArticlePath = $wgActionPaths['view'];

$wgServer = "{$_ENV['WIKI_SERVER_URL']}";
$wgCanonicalServer  = "{$_ENV['WIKI_SERVER_URL']}";

$wgForceHTTPS = true;
$wgMainPageIsDomainRoot = true;

$wgResourceBasePath = $wgScriptPath;

$wgFavicon = "$wgResourceBasePath/images/favicon.ico";

$wgLogos = [
	'icon' => "$wgResourceBasePath/images/DSP_Logo.png",
    'wordmark' => [
		'src' => "$wgResourceBasePath/images/DSP_Logo.png",
    ],
    'tagline' => [
		'src' => "$wgResourceBasePath/images/DSP_Logo.png",		// path to tagline version
		'width' => 135,
		'height' => 15,
	],
];


##################
#//*    Email 
##################

$wgEnableEmail = true;
$wgEnableUserEmail = true;

$wgEmergencyContact = "admin@dsp-wiki.com";
$wgPasswordSender = "no-reply@dsp-wiki.com";

wfLoadExtension( 'Echo' );

$wgAllowHTMLEmail = true;
$wgEnotifUserTalk = false;
$wgEnotifWatchlist = false;
$wgEmailAuthentication = true;
$wgEmailConfirmToEdit = true;

##################
#//*    AWS 
##################

$wgSMTP = [
  'host'      => 'tls://email-smtp.eu-west-2.amazonaws.com',
  'IDHost'    => 'email-smtp.eu-west-2.amazonaws.com',
  'port'      => 465,
  'auth'      => true,
  'username'  => $_ENV['WIKI_S3_KEY'],
  'password'  => $_ENV['WIKI_S3_SECRET']
];

#$wgAWSCredentials = [
#  'key' => $_ENV['WIKI_S3_KEY'],
#  'secret' => $_ENV['WIKI_S3_SECRET'],
#  'token' => false
#];
#$wgAWSBucketName = 'media.dsp-wiki.com';
#$wgAWSBucketDomain = 'media.dsp-wiki.com';
#$wgAWSRepoHashLevels = '2';
#$wgAWSRepoDeletedHashLevels = '3';
#$wgFileBackends['s3']['endpoint'] = 'https://eu-central-1.linodeobjects.com';
#$wgAWSRegion = 'eu-central-1';

##################
#//*    Database 
##################

$wgDBtype = "mysql";
$wgDBserver = "antts.uk";
$wgDBname = "{$_ENV['WIKI_DB_NAME']}";
$wgDBuser = "dspwiki";
$wgDBpassword = "{$_ENV['WIKI_DB_PASS']}";
$wgDBprefix = "";
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

##################
#//*    Cache 
##################

$wgSessionCacheType = CACHE_DB;
$wgMainCacheType = CACHE_NONE;

##################
#//*    Footer 
##################

$wgFooterIcons = [
  "poweredby" => [
    "mediawiki" => [
      "src" => "$wgScriptPath/images/badge-mediawiki.svg",
      "url" => "https://www.mediawiki.org",
      "alt" => "Powered by MediaWiki",
      "height" => "42",
      "width" => "127",
    ],
  ],
  "copyright" => [
    "copyright" => [
      "src" => "$wgScriptPath/images/badge-ccbysa.svg",
      "url" => "https://creativecommons.org/licenses/by-sa/4.0/",
      "alt" => "Creative Commons Attribution-ShareAlike",
      "height" => "50",
      "width" => "110",
    ]
  ]
];

$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "https://creativecommons.org/licenses/by-sa/4.0/";
$wgRightsText = "Creative Commons Attribution-ShareAlike";
$wgRightsIcon = "$wgResourceBasePath/resources/assets/licenses/cc-by-sa.png";

# Add links to footer
$wgHooks['SkinAddFooterLinks'][] = function ( $sk, $key, &$footerlinks ) {
	$rel = 'nofollow noreferrer noopener';

	if ( $key === 'places' ) {
		$footerlinks['analytics'] = Html::element(
			'a',
			[
				'href' => "https://analytics.dsp-wiki.com/{$_ENV['WIKI_PLAUSIBLE_DOMAIN']}",
				'rel' => $rel
			],
			$sk->msg( 'footer-analytics' )->text()
		);
	}
};

$wgULSLanguageDetection = false;

##################
#//*     Images
##################
$wgEnableUploads = true;
$wgGenerateThumbnailOnParse = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgFileExtensions = array_merge( $wgFileExtensions,
    array( 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'svg', 'xml' )
    ); 
$wgAllowTitlesInSVG = true;
$wgSVGConverter = 'ImageMagick';
$wgUseInstantCommons = true;  # do not allow InstantCommons
$wgPingback = false;           # do not ping back to Mediawiki.org with stats.
$wgShellLocale = "C.UTF-8";    # locale for shell commands
$wgGenerateThumbnailOnParse = true;
$wgThumbnailEpoch = "20190815000000";
$wgIgnoreImageErrors = true;
$wgMaxImageArea = 6.4e7;
$wgAllowExternalImagesFrom = ['https://cdn.akamai.steamstatic.com/', 'https://cdn.cloudflare.steamstatic.com'];
$wgNativeImageLazyLoading = true;

$wgLanguageCode = "en";
$wgLocaltimezone = "GMT";
$wgCacheDirectory = "$IP/cache";
$wgSecretKey = "{$_ENV['WIKI_SECRET_KEY']}";
$wgAuthenticationTokenVersion = "1";
#$wgUpgradeKey = "{$_ENV['WIKI_UPGRADE_KEY']}";
$wgDiff3 = "/usr/bin/diff3";

##################
#//*   Theme
##################

$wgDefaultSkin = "Citizen";
wfLoadSkin( 'Citizen' );
$wgCitizenThemeDefault = 'dark';
$wgDefaultMobileSkin = 'citizen';
$wgCitizenEnableCJKFonts = true;
$wgCitizenEnablePreferences = false;
$wgCitizenShowPageTools = 'permission';
$wgCitizenThemeColor = '#1D1D1D';
$wgAllowSiteCSSOnRestrictedPages = true;
wfLoadExtension( 'MobileFrontend' );

##################
#//*   Enabled extensions
##################

wfLoadExtension( 'AbuseFilter' );
wfLoadExtension( 'Antispam' );
wfLoadExtension( 'Cargo' );
wfLoadExtension( 'CategoryTree' );
wfLoadExtension( 'Capiunto' );
wfLoadExtension( 'Cite' );
wfLoadExtension( 'CheckUser' );
#wfLoadExtension( 'CiteThisPage' );
wfLoadExtension( 'CodeEditor' );
wfLoadExtension( 'CodeMirror' );
wfLoadExtension( 'ConfirmEdit' );
wfLoadExtension( 'CookieWarning' );
wfLoadExtension( 'CSS' );
wfLoadExtension( 'Gadgets' );
wfLoadExtension( 'Graph' );
wfLoadExtension( 'ImageMap' );
wfLoadExtension( 'InputBox' );
wfLoadExtension( 'JsonConfig' );
#wfLoadExtension( 'Math' );
wfLoadExtension( 'MultimediaViewer' );
wfLoadExtension( 'MultiPurge' );
wfLoadExtension( 'Nuke' );
wfLoadExtension( 'OATHAuth' );
#wfLoadExtension( 'WebAuthn' );
wfLoadExtension( 'PageImages' );
wfLoadExtension( 'PageViewInfo' );
wfLoadExtension( 'Plausible' );
wfLoadExtension( 'ParserFunctions' );
wfLoadExtension( 'PdfHandler' );
wfLoadExtension( 'ReplaceText' );
wfLoadExtension( 'Scribunto' );
wfLoadExtension( 'SecureLinkFixer' );
wfLoadExtension( 'SpamBlacklist' );
#wfLoadExtension( 'SmiteSpam' );
wfLoadExtension( 'SyntaxHighlight_GeSHi' );
wfLoadExtension( 'TemplateData' );
wfLoadExtension( 'TextExtracts' );
wfLoadExtension( 'TitleBlacklist' );
wfLoadExtension( 'VisualEditor' );
wfLoadExtension( 'WikiEditor' );
wfLoadExtension( 'TemplateStyles' );
wfLoadExtension( 'UserMerge' );

##################
#//*  Remove autoconfirmed
##################
unset( $wgGroupPermissions['autoconfirmed'] );
unset( $wgRevokePermissions['autoconfirmed'] );
unset( $wgAddGroups['autoconfirmed'] );
unset( $wgRemoveGroups['autoconfirmed'] );
unset( $wgGroupsAddToSelf['autoconfirmed'] );
unset( $wgGroupsRemoveFromSelf['autoconfirmed'] );
$wgImplicitGroups[] = 'autoconfirmed';

##################
#//*  Scribunto
##################
$wgScribuntoDefaultEngine = 'luastandalone';
$wgScribuntoUseGeSHi = true;
$wgScribuntoUseCodeEditor = true;
$wgTemplateDataUseGUI = false;

##################
#//*  Editors
##################
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";
$wgDefaultUserOptions['visualeditor-newwikitext'] = 1;
$wgPrefs[] = 'visualeditor-enable';
$wgVisualEditorEnableWikitext = true;
$wgVisualEditorEnableDiffPage = true;
$wgVisualEditorUseSingleEditTab = true;
$wgVisualEditorEnableVisualSectionEditing = true;

##################
#//*   Plausible
##################
$wgPlausibleDomain = 'https://analytics.dsp-wiki.com';
$wgPlausibleDomainKey = "{$_ENV['WIKI_PLAUSIBLE_DOMAIN']}";
$wgPlausibleHonorDNT = true;
$wgPlausibleTrackLoggedIn = true;
$wgPlausibleTrackOutboundLinks = true;
$wgPlausibleIgnoredTitles = [ '/Special:*' ];
$wgPlausibleEnableCustomEvents = true;
$wgPlausibleTrack404 = true;
$wgPlausibleTrackSearchInput = true;
$wgPlausibleTrackEditButtonClicks = true;
$wgPlausibleTrackCitizenSearchLinks = true;
$wgPlausibleTrackCitizenMenuLinks = true;
$wgPlausibleApiKey = "{$_ENV['WIKI_PLAUSIBLE_API']}";

##################
#//*   Discord
##################
wfLoadExtension( 'DiscordRCFeed' );
$wgRCFeeds['discord'] = [
	'url' => $_ENV['WIKI_DISCORD_URL'],
    'omit_talk' => true,
];
$wgRCFeeds['discord']['request_replace'] = [
	'username' => $_ENV['WIKI_DISCORD_NAME'],
    'avatar_url' => $_ENV['WIKI_DISCORD_LOGO'],
];
$wgRCFeeds['discord']['omit_log_types'] = [
	'newusers',
];

##################
#//*   Translation
##################
wfLoadExtension( 'Babel' );
wfLoadExtension( 'cldr' );
wfLoadExtension( 'CleanChanges' );
$wgCCTrailerFilter = true;
$wgCCUserFilter = false;
$wgDefaultUserOptions['usenewrc'] = 1;
wfLoadExtension( 'Translate' );
$wgTranslateDocumentationLanguageCode = 'qqq';
$wgTranslateNewsletterPreference = false;
$wgTranslateFuzzyBotName = 'FuzzyBot';
$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages
$wgEnablePageTranslation = true;
$wgPageTranslationNamespace = 1198;
$wgTranslatePageTranslationULS = false;
wfLoadExtension( 'UniversalLanguageSelector' );

##################
#//*   TemplateStyles
##################
$wgTemplateStylesAllowedUrls = [
    "audio" => [""],
    "image" => ["<^/skins/common/images/>"],
    "svg" => [""],
    "font" => ["<^/skins/common/font/>"],
    "namespace" => ["<.>"],
    "css" => []
];
$wgInvalidateCacheOnLocalSettingsChange = true;

define("NS_MODDING", 3000);
define("NS_MODDING_TALK", 3001);
$wgExtraNamespaces[NS_MODDING] = "Modding";
$wgExtraNamespaces[NS_MODDING_TALK] = "Modding_Talk";
$wgContentNamespaces[] = NS_MODDING;
$wgNamespacesToBeSearchedDefault[NS_MODDING] = true;


##################
#//*      Permissions
##################
$wgUserMergeProtectedGroups = [];
$wgNamespaceProtection[NS_TEMPLATE] = ['edittemplate'];

# all
$wgGroupPermissions['*']['createaccount'] = true;
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['createtalk'] = false;
$wgGroupPermissions['*']['createpage'] =  false;
$wgGroupPermissions['*']['writeapi'] = true;
$wgGroupPermissions['*']['editmyprivateinfo'] =  false;
$wgGroupPermissions['*']['editmywatchlist'] =  false;
$wgGroupPermissions['*']['viewmyprivateinfo'] =  false;
$wgGroupPermissions['*']['viewmywatchlist'] =  false;
$wgGroupPermissions['*']['skipcaptcha'] = false;


#user not confirmed
$wgGroupPermissions['user']['oathauth-enable'] = true;
$wgGroupPermissions['user']['edit'] =  false;
$wgGroupPermissions['user']['createpage'] =  false;
$wgGroupPermissions['user']['changetags'] =  false;
$wgGroupPermissions['user']['applychangetags'] =  false;
$wgGroupPermissions['user']['createtalk'] =  false;
$wgGroupPermissions['user']['editcontentmodel'] =  false;
$wgGroupPermissions['user']['move'] =  false;
$wgGroupPermissions['user']['upload'] =  false;
$wgGroupPermissions['user']['editmyusercss'] =  false;
$wgGroupPermissions['user']['editmyuserjson'] =  false;
$wgGroupPermissions['user']['editmyuserjs'] =  false;
$wgGroupPermissions['user']['editmyuserjsredirect'] =  false;
$wgGroupPermissions['user']['minoredit'] =  false;
$wgGroupPermissions['user']['move-categorypages'] =  false;
$wgGroupPermissions['user']['movefile'] =  false;
$wgGroupPermissions['user']['move-subpages'] =  false;
$wgGroupPermissions['user']['move-rootuserpages'] =  false;
$wgGroupPermissions['user']['reupload-shared'] =  false;
$wgGroupPermissions['user']['reupload'] =  false;
$wgGroupPermissions['user']['sendemail'] =  false;
$wgGroupPermissions['user']['upload'] =  false;
$wgGroupPermissions['user']['skipcaptcha'] = false;


#emailconfirmed
$wgGroupPermissions['emailconfirmed']['edit'] = true;
$wgGroupPermissions['emailconfirmed']['createpage'] =  true;
$wgGroupPermissions['emailconfirmed']['changetags'] =  true;
$wgGroupPermissions['emailconfirmed']['applychangetags'] =  true;
$wgGroupPermissions['emailconfirmed']['createtalk'] =  true;
$wgGroupPermissions['emailconfirmed']['editcontentmodel'] =  true;
$wgGroupPermissions['emailconfirmed']['move'] =  true;
$wgGroupPermissions['emailconfirmed']['upload'] =  true;
$wgGroupPermissions['emailconfirmed']['editmyusercss'] =  true;
$wgGroupPermissions['emailconfirmed']['editmyuserjson'] =  true;
$wgGroupPermissions['emailconfirmed']['editmyuserjs'] =  true;
$wgGroupPermissions['emailconfirmed']['editmyuserjsredirect'] =  true;
$wgGroupPermissions['emailconfirmed']['minoredit'] =  true;
$wgGroupPermissions['emailconfirmed']['move-categorypages'] =  true;
$wgGroupPermissions['emailconfirmed']['movefile'] =  true;
$wgGroupPermissions['emailconfirmed']['move-subpages'] =  true;
$wgGroupPermissions['emailconfirmed']['move-rootuserpages'] =  true;
$wgGroupPermissions['emailconfirmed']['reupload-shared'] =  true;
$wgGroupPermissions['emailconfirmed']['reupload'] =  true;
$wgGroupPermissions['emailconfirmed']['sendemail'] =  true;
$wgGroupPermissions['emailconfirmed']['editmyprivateinfo'] =  true;
$wgGroupPermissions['emailconfirmed']['editmywatchlist'] =  true;
$wgGroupPermissions['emailconfirmed']['viewmyprivateinfo'] =  true;
$wgGroupPermissions['emailconfirmed']['viewmywatchlist'] =  true;
$wgGroupPermissions['emailconfirmed']['upload'] =  true;
$wgGroupPermissions['emailconfirmed']['writeapi'] = true;
$wgGroupPermissions['emailconfirmed']['translate'] = true;
$wgGroupPermissions['emailconfirmed']['translate-messagereview'] = true;
$wgGroupPermissions['emailconfirmed']['translate-groupreview'] = true;
$wgGroupPermissions['emailconfirmed']['translate-import'] = true;
$wgGroupPermissions['emailconfirmed']['skipcaptcha'] = false;

#template
#$wgGroupPermissions['templates'] =$wgGroupPermissions['emailconfirmed'];
$wgGroupPermissions['templates']['edittemplate'] =  true;

#bureaucrat
$wgGroupPermissions['bureaucrat'] =$wgGroupPermissions['sysop'];
$wgGroupPermissions['bureaucrat']['usermerge'] = true;
$wgGroupPermissions['bureaucrat']['checkuser'] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = true;
$wgGroupPermissions['bureaucrat']['checkuser-log'] = true;
$wgGroupPermissions['bureaucrat']['investigate'] = true;
$wgGroupPermissions['bureaucrat']['checkuser-temporary-account'] = true;
$wgImplicitGroups[] = 'bureaucrat';

#sys op
$wgGroupPermissions['sysop'] =$wgGroupPermissions['templates'];
$wgGroupPermissions['sysop']['checkuser-log'] = true;
$wgGroupPermissions['sysop']['investigate'] = true;
$wgGroupPermissions['sysop']['checkuser-temporary-account'] = true;
$wgGroupPermissions['sysop']['sboverride'] = true;
$wgGroupPermissions['sysop']['pagetranslation'] = true;
$wgGroupPermissions['sysop']['translate-manage'] = true;
$wgGroupPermissions['sysop']['editinterface'] = true;
$wgGroupPermissions['sysop']['skipcaptcha'] = true;
$wgGroupPermissions['sysop']['cleantalk-bypass'] = true;


$wgGroupPermissions['bot']['cleantalk-bypass'] = true;
$wgGroupPermissions['bot']['skipcaptcha'] = true;

$wgAutopromote['emailconfirmed'] = APCOND_EMAILCONFIRMED;
$wgImplicitGroups[] = 'emailconfirmed';

#################
#//*     DEV
#################
$wgShowExceptionDetails = false;

#################
#//*    SPAM
#################
$wgCTAccessKey = "{$_ENV['WIKI_CT_KEY']}";
$wgCTMinEditCount = 10;
$wgCTShowLink = false;
#$wgSpamRegex = ["/online-casino|casino|buy-viagra|adipex|phentermine|lidocaine|milf|adult-website\.com|display:none|overflow:\s*auto;\s*height:\s*[0-4]px;/i"];
wfLoadExtensions([ 'ConfirmEdit', 'ConfirmEdit/ReCaptchaNoCaptcha' ]);
$wgCaptchaClass = 'ReCaptchaNoCaptcha';
$wgReCaptchaSiteKey = "{$_ENV['WIKI_CAP_KEY']}";
$wgReCaptchaSecretKey = "{$_ENV['WIKI_CAP_SECRET']}";
$wgCaptchaTriggers['edit'] = true;
$wgCaptchaTriggers['create'] = true;
$wgCaptchaTriggers['addurl'] = true;
$wgCaptchaTriggers['createaccount'] = true;
$wgCaptchaTriggers['badlogin'] = true;
$wgAllowConfirmedEmail = true;
$wgBlacklistSettings = [
	'spam' => [
		'files' => [
			"https://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1",
			"https://en.wikipedia.org/w/index.php?title=MediaWiki:Spam-blacklist&action=raw&sb_ver=1"
		],
	],
];
$wgEnableDnsBlacklist = true;
$wgDnsBlacklistUrls = array( 'xbl.spamhaus.org', 'dnsbl.tornevall.org' );
$wgSmiteSpamIgnoreSmallPages = false;

#################
#//*    CDN
#################
$wgUseCdn = true;
$wgUsePrivateIPs = true;
$wgCdnServersNoPurge = ['10.0.0.0/8',	'173.245.48.0/20',	'103.21.244.0/22',	'103.22.200.0/22',	'103.31.4.0/22',	'141.101.64.0/18',	'108.162.192.0/18',	'190.93.240.0/20',	'188.114.96.0/20',	'197.234.240.0/22',	'198.41.128.0/17',	'162.158.0.0/15',	'104.16.0.0/13',	'104.24.0.0/14',	'172.64.0.0/13',	'131.0.72.0/22',	'2400:cb00::/32',	'2606:4700::/32',	'2803:f800::/32',	'2405:b500::/32',	'2405:8100::/32',	'2a06:98c0::/29',	'2c0f:f248::/32',  '2405:b500::/32'];
$wgMultiPurgeEnabledServices = array ( 'Cloudflare' );
$wgMultiPurgeServiceOrder = array ( 'Cloudflare' );
$wgMultiPurgeCloudFlareZoneId = "{$_ENV['WIKI_CF_ID']}";
$wgMultiPurgeCloudflareApiToken = "{$_ENV['WIKI_CF_API']}";

#################
#//*    Cookies
#################
$wgCookieWarningEnabled = true;
$wgCookieSecure = true;
$wgCookieSameSite = 'Strict';

#################
#//*    CSP
#################
$wgReferrerPolicy = array('strict-origin-when-cross-origin', 'strict-origin');
$wgCSPHeader = [
  'useNonces' => true,
  'unsafeFallback' => false,
  'default-src' => ['\'self\'', 'https://www.recaptcha.net', 'https://analytics.dsp-wiki.com'],
  'script-src' => [ '\'self\'', '\'sha256-fZolVpA0hfg4qTFqcgfmgUvHzo0qL28/odWGiD5Bc7U=\'', 'https://analytics.dsp-wiki.com'],
	'style-src' => [ '\'self\''],
	'object-src' => [ '\'none\'' ],
];

#################
#//*    Json
#################
$wgJsonConfigEnableLuaSupport = true;
$wgJsonConfigModels['Tabular.JsonConfig'] = 'JsonConfig\JCTabularContent';
$wgJsonConfigs['Tabular.JsonConfig'] = [ 
        'namespace' => 486, 
        'nsName' => 'Data',
        'pattern' => '/.\.tab$/',
        'license' => 'CC0-1.0',
        'isLocal' => false,
];
$wgJsonConfigModels['Map.JsonConfig'] = 'JsonConfig\JCMapDataContent';
$wgJsonConfigs['Map.JsonConfig'] = [ 
        'namespace' => 486,
        'nsName' => 'Data',
        'pattern' => '/.\.map$/',
        'license' => 'CC0-1.0',
        'isLocal' => false,
];
$wgJsonConfigInterwikiPrefix = "commons";
$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [ 
        'url' => 'https://commons.wikimedia.org/w/api.php'
];
$wgJsonConfigs['Map.JsonConfig']['remote'] = [
        'url' => 'https://commons.wikimedia.org/w/api.php'
];

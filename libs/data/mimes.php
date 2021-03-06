<?php
    $mimes  = array(
    'video/x-ms-asf'=>'asx',
    'text/xml'=>'xml',
    'text/tab-separated-values'=>'tsv',
    'audio/x-pn-realaudio'=>'ra',
    'application/x-sv4crc'=>'sv4crc',
    'application/x-pkcs7-certificates'=>'spc',
    'application/x-perfmon'=>'pmc',
    'application/x-ms-reader'=>'lit',
    'application/x-mscardfile'=>'crd',
    'application/x-internet-signup'=>'isp',
    'application/vnd.wap.wmlscriptc'=>'wmlsc',
    'application/vnd.visio'=>'vst',
    'application/vnd.ms-excel.addin.macroEnabled.12'=>'xlam',
    'application/octet-stream'=>'ttf',
    'application/octet-stream'=>'pfm',
    'application/octet-stream'=>'csv',
    'application/octet-stream'=>'aaf',
    'application/onenote'=>'one',
    'application/hta'=>'hta',
    'application/atom+xml'=>'atom',
    'text/h323'=>'323',
    'message/rfc822'=>'mhtml',
    'audio/mid'=>'midi',
    'application/x-pkcs7-certreqresp'=>'p7r',
    'application/x-msmoney'=>'mny',
    'application/x-msclip'=>'clp',
    'application/vnd.visio'=>'vsd',
    'application/octet-stream'=>'lpk',
    'application/octet-stream'=>'bin',
    'application/onenote'=>'onetoc',
    'application/directx'=>'x',
    'video/x-ms-wvx'=>'wvx',
    'text/x-vcard'=>'vcf',
    'text/x-component'=>'htc',
    'text/webviewhtml'=>'htt',
    'text/plain'=>'h',
    'message/rfc822'=>'mht',
    'audio/mid'=>'mid',
    'application/x-pkcs7-certificates'=>'p7b',
    'application/zip'=>'zip',
    'application/x-gzip'=>'gz',
    'application/x-dvi'=>'dvi',
    'application/x-cpio'=>'cpio',
    'application/vnd.ms-visio.viewer'=>'vdx',
    'application/vnd.ms-powerpoint.slide.macroEnabled.12'=>'sldm',
    'application/vnd.ms-excel'=>'xlm',
    'application/vnd.fdf'=>'fdf',
    'application/set-registration-initiation'=>'setreg',
    'application/postscript'=>'eps',
    'application/pkcs7-signature'=>'p7s',
    'application/octet-stream'=>'toc',
    'application/octet-stream'=>'mdp',
    'application/octet-stream'=>'ics',
    'application/octet-stream'=>'chm',
    'application/octet-stream'=>'asi',
    'application/octet-stream'=>'afm',
    'application/envoy'=>'evy',
    'video/x-ms-wmp'=>'wmp',
    'video/quicktime'=>'qt',
    'video/mpeg'=>'mpv2',
    'text/xml'=>'xslt',
    'text/x-setext'=>'etx',
    'image/cis-cod'=>'cod',
    'audio/basic'=>'snd',
    'audio/basic'=>'au',
    'application/x-troff-man'=>'man',
    'application/x-quicktimeplayer'=>'qtl',
    'application/x-perfmon'=>'pmw',
    'application/x-java-applet'=>'class',
    'application/x-iphone'=>'iii',
    'application/x-csh'=>'csh',
    'application/x-compress'=>'z',
    'application/vnd.visio'=>'vtx',
    'application/vnd.visio'=>'vsw',
    'application/vnd.ms-works'=>'wps',
    'application/vnd.openxmlformats-officedocument.presentationml.template'=>'potx',
    'application/postscript'=>'ps',
    'application/pkcs7-mime'=>'p7c',
    'application/octet-stream'=>'thn',
    'application/octet-stream'=>'mso',
    'application/msword'=>'dot',
    'application/msword'=>'doc',
    'text/sgml'=>'sgml',
    'message/rfc822'=>'nws',
    'image/x-portable-bitmap'=>'pbm',
    'image/ief'=>'ief',
    'audio/wav'=>'wav',
    'application/x-texinfo'=>'texi',
    'application/x-msmediaview'=>'mvb',
    'application/x-hdf'=>'hdf',
    'application/vnd.visio'=>'vsx',
    'application/vnd.ms-word.template.macroEnabled.12'=>'dotm',
    'application/vnd.ms-word.document.macroEnabled.12'=>'docm',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation'=>'pptx',
    'application/octet-stream'=>'psm',
    'application/octet-stream'=>'java',
    'application/octet-stream'=>'eot',
    'application/java-archive'=>'jar',
    'video/mpeg'=>'mpeg',
    'text/xml'=>'xsf',
    'text/plain'=>'map',
    'text/iuls'=>'uls',
    'image/vnd.rn-realflash'=>'rf',
    'audio/x-mpegurl'=>'m3u',
    'audio/x-ms-wma'=>'wma',
    'audio/aiff'=>'aifc',
    'application/x-msaccess'=>'mdb',
    'application/x-miva-compiled'=>'mvc',
    'application/vnd.ms-pki.stl'=>'stl',
    'application/vnd.openxmlformats-officedocument.presentationml.slideshow'=>'ppsx',
    'application/vnd.ms-excel.sheet.binary.macroEnabled.12'=>'xlsb',
    'application/set-payment-initiation'=>'setpay',
    'application/octet-stream'=>'prm',
    'application/octet-stream'=>'mix',
    'application/octet-stream'=>'lzh',
    'application/octet-stream'=>'hhk',
    'application/onenote'=>'onepkg',
    'x-world/x-vrml'=>'xaf',
    'x-world/x-vrml'=>'flr',
    'video/x-ivf'=>'IVF',
    'text/plain'=>'cnf',
    'text/plain'=>'asm',
    'image/tiff'=>'tiff',
    'audio/x-ms-wax'=>'wax',
    'application/x-troff-ms'=>'ms',
    'application/x-tcl'=>'tcl',
    'application/x-shar'=>'shar',
    'application/x-sh'=>'sh',
    'application/x-netcdf'=>'nc',
    'application/winhlp'=>'hlp',
    'application/oda'=>'oda',
    'application/octet-stream'=>'pfb',
    'application/octet-stream'=>'fla',
    'video/x-ms-wm'=>'wm',
    'image/x-rgb'=>'rgb',
    'image/x-portable-pixmap'=>'ppm',
    'audio/x-pn-realaudio'=>'ram',
    'application/x-stuffit'=>'sit',
    'application/x-director'=>'dir',
    'application/vnd.ms-project'=>'mpp',
    'application/vnd.ms-excel'=>'xla',
    'application/streamingmedia'=>'ssm',
    'application/olescript'=>'axs',
    'application/oleobject'=>'ods',
    'application/octet-stream'=>'psp',
    'application/octet-stream'=>'jpb',
    'x-world/x-vrml'=>'wrz',
    'video/mpeg'=>'m1v',
    'text/xml'=>'mno',
    'image/x-cmx'=>'cmx',
    'image/jpeg'=>'jpeg',
    'image/bmp'=>'dib',
    'audio/mid'=>'rmi',
    'audio/aiff'=>'aiff',
    'application/x-ms-wmd'=>'wmd',
    'application/x-mswrite'=>'wri',
    'application/x-mspublisher'=>'pub',
    'application/x-internet-signup'=>'ins',
    'application/vnd.ms-works'=>'wks',
    'application/vnd.ms-excel'=>'xls',
    'application/postscript'=>'ai',
    'application/pkix-crl'=>'crl',
    'application/octet-stream'=>'qxd',
    'application/octet-stream'=>'dwp',
    'x-world/x-vrml'=>'xof',
    'video/x-ms-wmv'=>'wmv',
    'video/x-ms-asf'=>'nsc',
    'video/mpeg'=>'mpa',
    'image/x-portable-anymap'=>'pnm',
    'audio/x-pn-realaudio-plugin'=>'rpm',
    'audio/x-aiff'=>'aif',
    'application/x-troff-me'=>'me',
    'application/x-perfmon'=>'pml',
    'application/x-msterminal'=>'trm',
    'application/x-msmediaview'=>'m13',
    'application/x-javascript'=>'js',
    'application/x-director'=>'dxr',
    'application/vnd.ms-powerpoint.template.macroEnabled.12'=>'potm',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.template'=>'xltx',
    'application/vnd.ms-excel'=>'xlt',
    'application/vnd.ms-excel'=>'xlc',
    'application/pkcs10'=>'p10',
    'application/octet-stream'=>'smi',
    'application/octet-stream'=>'sea',
    'application/mac-binhex40'=>'hqx',
    'application/futuresplash'=>'spl',
    'video/x-sgi-movie'=>'movie',
    'video/x-la-asf'=>'lsf',
    'text/plain'=>'txt',
    'image/pjpeg'=>'jfif',
    'image/jpeg'=>'jpe',
    'application/x-zip-compressed'=>'zip',
    'application/x-msmetafile'=>'wmf',
    'application/x-msmediaview'=>'m14',
    'application/x-latex'=>'latex',
    'application/vnd.ms-works'=>'wcm',
    'application/vnd.ms-powerpoint.presentation.macroEnabled.12'=>'pptm',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
    'application/octet-stream'=>'hhp',
    'application/octet-stream'=>'aca',
    'application/msaccess'=>'accdb',
    'application/liquidmotion'=>'jcz',
    'x-world/x-vrml'=>'wrl',
    'video/x-ms-wmx'=>'wmx',
    'video/x-ms-asf'=>'asr',
    'video/x-la-asf'=>'lsx',
    'text/xml'=>'xsl',
    'text/html'=>'html',
    'image/tiff'=>'tif',
    'application/x-x509-ca-cert'=>'der',
    'application/x-pkcs12'=>'pfx',
    'application/x-pkcs12'=>'p12',
    'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'=>'ppsm',
    'application/octet-stream'=>'cur',
    'application/msaccess'=>'accdt',
    'text/x-hdml'=>'hdml',
    'text/html'=>'htm',
    'image/x-xbitmap'=>'xbm',
    'image/jpeg'=>'jpg',
    'application/x-texinfo'=>'texinfo',
    'application/vnd.ms-powerpoint.addin.macroEnabled.12'=>'ppam',
    'application/vnd.ms-excel'=>'xlw',
    'application/vnd.rn-realmedia'=>'rm',
    'application/pdf'=>'pdf',

    'application/octet-stream'=>'rar',
    'application/octet-stream'=>'psd',
    'application/octet-stream'=>'inf',
    'application/octet-stream'=>'emz',
    'application/octet-stream'=>'dsp',
    'application/onenote'=>'onea',
    'application/liquidmotion'=>'jck',
    'video/mpeg'=>'mpe',
    'video/mpeg'=>'mp2',
    'text/scriptlet'=>'sct',
    'image/x-cmu-raster'=>'ras',
    'application/x-shockwave-flash'=>'swf',
    'application/x-ms-wmz'=>'wmz',
    'application/x-gtar'=>'gtar',
    'application/x-director'=>'dcr',
    'application/vnd.openxmlformats-officedocument.presentationml.slide'=>'sldx',
    'application/vnd.ms-pps'=>'pps',
    'application/pkcs7-mime'=>'p7m',
    'application/octet-stream'=>'xsn',
    'application/octet-stream'=>'ocx',
    'application/msaccess'=>'accde',
    'video/quicktime'=>'mov',
    'text/vnd.wap.wmlscript'=>'wmls',
    'text/plain'=>'cpp',
    'text/plain'=>'c',
    'text/plain'=>'bas',
    'text/css'=>'css',
    'image/x-jg'=>'art',
    'audio/mpeg'=>'mp3',
    'application/x-troff'=>'t',
    'application/x-troff'=>'roff',
    'application/x-tar'=>'tar',
    'application/x-oleobject'=>'hhc',
    'application/x-msschedule'=>'scd',
    'application/vnd.ms-pki.pko'=>'pko',
    'application/vnd.ms-pki.certstore'=>'sst',
    'application/vnd.ms-powerpoint'=>'ppt',
    'application/octet-stream'=>'xtp',
    'application/octet-stream'=>'u32',
    'application/octet-stream'=>'pcx',
    'application/octet-stream'=>'msi',
    'application/octet-stream'=>'exe',
    'application/octet-stream'=>'asd',
    'application/onenote'=>'onetoc2',
    'application/fractals'=>'fif',
    'video/mpeg'=>'mpg',
    'text/xml'=>'vml',
    'text/plain'=>'xdr',
    'text/plain'=>'vcs',
    'text/html'=>'hxt',
    'message/rfc822'=>'eml',
    'image/x-xpixmap'=>'xpm',
    'image/x-icon'=>'ico',
    'image/gif'=>'gif',
    'drawing/x-dwf'=>'dwf',
    'application/x-wais-source'=>'src',
    'application/x-troff'=>'tr',
    'application/x-perfmon'=>'pmr',
    'application/x-perfmon'=>'pma',
    'application/x-msdownload'=>'dll',
    'application/x-bcpio'=>'bcpio',
    'application/vnd.wap.wmlc'=>'wmlc',
    'application/vnd.ms-works'=>'wdb',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.template'=>'dotx',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx',
    'application/vnd.ms-powerpoint'=>'pot',
    'application/vnd.ms-excel.template.macroEnabled.12'=>'xltm',
    'application/rtf'=>'rtf',
    'application/pics-rules'=>'prf',
    'application/octet-stream'=>'snp',
    'application/octet-stream'=>'cab',
    'video/x-msvideo'=>'avi',
    'video/x-ms-asf'=>'asf',
    'text/xml'=>'dtd',
    'text/vnd.wap.wml'=>'wml',
    'text/vbscript'=>'vbs',
    'text/richtext'=>'rtx',
    'text/dlm'=>'dlm',
    'image/x-xwindowdump'=>'xwd',
    'image/x-portable-graymap'=>'pgm',
    'image/bmp'=>'bmp',
    'application/x-x509-ca-cert'=>'crt',
    'application/x-ustar'=>'ustar',
    'application/x-tex'=>'tex',
    'application/x-sv4cpio'=>'sv4cpio',
    'application/x-compressed'=>'tgz',
    'application/x-cdf'=>'cdf',
    'application/vnd.visio'=>'vss',
    'application/vnd.ms-pki.seccat'=>'cat',
    'application/vnd.ms-officetheme'=>'thmx',
    'application/vnd.ms-excel.sheet.macroEnabled.12'=>'xlsm',
    'application/octet-stream'=>'bin',
    'application/onenote'=>'onetmp',
    'application/internet-property-stream'=>'acx',
    'text/xml'=>'wsdl',
    'text/xml'=>'disco',
    'text/xml'=>'xsd',
    'image/vnd.wap.wbmp'=>'wbmp',
    'image/png'=>'png',
    'image/png'=>'pnz',
    'audio/x-smd'=>'smd',
    'audio/x-smd'=>'smz',
    'audio/x-smd'=>'smx',
    'application/x-smaf'=>'mmf'
);
return $mimes;
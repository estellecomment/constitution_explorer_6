# $Id: htaccess.inc 811 2008-06-18 23:13:46Z ksharp $

AuthType WebAuth
require valid-user

WebAuthLdapAttribute displayName
WebAuthLdapAttribute suAffiliation

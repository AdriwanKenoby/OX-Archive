![Logo of the project](https://raw.githubusercontent.com/jehna/readme-best-practices/master/sample-logo.png)
# OX-Archive
## Installing / Getting started
[generate FHIR classes if you want to use PHPFHIRResponseParser] php bin/console archive:fhir:generate-classes

## Apache 2.4 configuration
#<VirtualHost *:80>
#    
#    DocumentRoot "/var/www/html/OX-Archive/web"
#    DirectoryIndex index.php
#    ServerName ox-archive
#    
#    Alias /web/ /var/www/html/OX-Archive/web/
#    <Directory "/var/www/html/OX-Archive/web">
#        AllowOverride All
#        FallbackResource /web/index.php
#    </Directory>
#</VirtualHost>

# PowerShell script to configure FastCGI for PHP in IIS
# Run this as Administrator

Import-Module WebAdministration -SkipEditionCheck

# Check if FastCGI application already exists
$fastCgiExists = Get-WebConfiguration -Filter "/system.webServer/fastCgi/application[@fullPath='C:\php\php-cgi.exe']" -ErrorAction SilentlyContinue

if (-not $fastCgiExists) {
    Write-Host "Adding FastCGI application for PHP..."
    
    try {
        # Use appcmd.exe which is more reliable for FastCGI configuration
        $appcmd = "$env:SystemRoot\System32\inetsrv\appcmd.exe"
        
        if (Test-Path $appcmd) {
            # Add FastCGI application
            & $appcmd set config -section:system.webServer/fastCgi /+"[fullPath='C:\php\php-cgi.exe',maxInstances='4',idleTimeout='600',activityTimeout='30',requestTimeout='90',instanceMaxRequests='10000',protocol='NamedPipe',flushNamedPipe='False']" /commit:apphost
            
            # Add environment variables
            & $appcmd set config -section:system.webServer/fastCgi /+"[fullPath='C:\php\php-cgi.exe'].environmentVariables.[name='PHP_FCGI_MAX_REQUESTS',value='10000']" /commit:apphost
            & $appcmd set config -section:system.webServer/fastCgi /+"[fullPath='C:\php\php-cgi.exe'].environmentVariables.[name='PHPRC',value='C:\php\']" /commit:apphost
            
            Write-Host "FastCGI application configured successfully using appcmd!"
        } else {
            Write-Host "appcmd.exe not found. Trying alternative method..."
            
            # Alternative: Use New-WebConfigurationProperty
            $config = Get-WebConfiguration -Filter "/system.webServer/fastCgi"
            New-WebConfigurationProperty -PSPath "MACHINE/WEBROOT/APPHOST" -Filter "system.webServer/fastCgi" -Name "application" -Value @{
                fullPath = "C:\php\php-cgi.exe"
                maxInstances = "4"
                idleTimeout = "600"
                activityTimeout = "30"
                requestTimeout = "90"
                instanceMaxRequests = "10000"
                protocol = "NamedPipe"
                flushNamedPipe = "False"
            }
            
            # Add environment variables
            $filter = "system.webServer/fastCgi/application[@fullPath='C:\php\php-cgi.exe']"
            New-WebConfigurationProperty -PSPath "MACHINE/WEBROOT/APPHOST" -Filter $filter -Name "environmentVariables" -Value @{name="PHP_FCGI_MAX_REQUESTS"; value="10000"}
            New-WebConfigurationProperty -PSPath "MACHINE/WEBROOT/APPHOST" -Filter $filter -Name "environmentVariables" -Value @{name="PHPRC"; value="C:\php\"}
            
            Write-Host "FastCGI application configured successfully using PowerShell cmdlets!"
        }
    } catch {
        Write-Host "Error: $_" -ForegroundColor Red
        Write-Host "Please configure FastCGI manually via IIS Manager:" -ForegroundColor Yellow
        Write-Host "1. Open IIS Manager" -ForegroundColor Yellow
        Write-Host "2. Select server node" -ForegroundColor Yellow
        Write-Host "3. Double-click 'FastCGI Settings'" -ForegroundColor Yellow
        Write-Host "4. Click 'Add Application' and enter:" -ForegroundColor Yellow
        Write-Host "   Full Path: C:\php\php-cgi.exe" -ForegroundColor Yellow
        Write-Host "   Max Instances: 4" -ForegroundColor Yellow
        Write-Host "   Then add environment variables PHP_FCGI_MAX_REQUESTS=10000 and PHPRC=C:\php\" -ForegroundColor Yellow
    }
} else {
    Write-Host "FastCGI application already exists."
}

Write-Host ""
Write-Host "Configuration complete. Please restart IIS or the application pool."
Write-Host "You can restart IIS with: iisreset"

<?php

include_once "config.php";

$targetsListRegex        = '/^[\da-zA-Z-. \/]+$/';
$hostsListRegex          = '/^[\da-zA-Z-.,:\/]+$/';
$protocolePortsListRegex = '/^(([TU]:)?[0-9\-]+|[a-z\-]+)(,([TU]:)?[0-9\-]+|,[a-z\-]+)*$/';
$portsListRegex          = '/^([0-9\-]+|[a-z\-]+)(,[0-9\-]+|,[a-z\-]+)*$/';
$tempoRegex              = '/^\d+[smh]?$/';
$fileNameRegex           = '/^[^<>:\/|?]+$/';

$targets = filter_input(INPUT_GET, 'targets', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $targetsListRegex], "flags" => FILTER_NULL_ON_FAILURE]);
$preset  = filter_input(INPUT_GET, "preset", FILTER_SANITIZE_STRING);

if ($preset && isset($presets[$preset])) {
  $options = $presets[$preset];
} else {
  $options = filter_input_array(INPUT_GET, [
// TARGET SPECIFICATION:
    '-iR'       => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--exclude' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $hostsListRegex]],
// HOST DISCOVERY:
    '-sL'           => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $hostsListRegex]],
    '-sP'           => FILTER_VALIDATE_BOOLEAN,
    '-P0'           => FILTER_VALIDATE_BOOLEAN,
    '-Pn'           => FILTER_VALIDATE_BOOLEAN,
    '-PS'           => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $portsListRegex]],
    '-PA'           => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $portsListRegex]],
    '-PU'           => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $portsListRegex]],
    '-PE'           => FILTER_VALIDATE_BOOLEAN,
    '-PP'           => FILTER_VALIDATE_BOOLEAN,
    '-PM'           => FILTER_VALIDATE_BOOLEAN,
    '-PO'           => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0, 'max_range' => 255]],
    '-PR'           => FILTER_VALIDATE_BOOLEAN,
    '--send-ip'     => FILTER_VALIDATE_BOOLEAN,
    '-n'            => FILTER_VALIDATE_BOOLEAN,
    '-R'            => FILTER_VALIDATE_BOOLEAN,
    '--dns-servers' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $hostsListRegex]],
// SCAN TECHNIQUES:
    '-sS'          => FILTER_VALIDATE_BOOLEAN,
    '-sT'          => FILTER_VALIDATE_BOOLEAN,
    '-sA'          => FILTER_VALIDATE_BOOLEAN,
    '-sW'          => FILTER_VALIDATE_BOOLEAN,
    '-sM'          => FILTER_VALIDATE_BOOLEAN,
    '-sF'          => FILTER_VALIDATE_BOOLEAN,
    '-sN'          => FILTER_VALIDATE_BOOLEAN,
    '-sX'          => FILTER_VALIDATE_BOOLEAN,
    '-sU'          => FILTER_VALIDATE_BOOLEAN,
    '--scanflags'  => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^(URG|ACK|PSH|RST|SYN|FIN|,)+|[1-9]?[0-9]|[1-2][0-9][0-9]$/']],
    '-sI'          => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[a-zA-Z\d:.-]+(:\d+)?$/']],
    '-sO'          => FILTER_VALIDATE_BOOLEAN,
    '-b'           => FILTER_VALIDATE_URL,
    '--traceroute' => FILTER_VALIDATE_BOOLEAN,
    '--reason'     => FILTER_VALIDATE_BOOLEAN,
// PORT SPECIFICATION AND SCAN ORDER:
    '-p'           => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $portsListRegex]],
    '-F'           => FILTER_VALIDATE_BOOLEAN,
    '-r'           => FILTER_VALIDATE_BOOLEAN,
    '--top-ports'  => FILTER_VALIDATE_INT,
    '--port-ratio' => ['filter' => FILTER_VALIDATE_FLOAT, 'options' => ['min_range' => 0, 'max_range' => 1]],
// SERVICE/VERSION DETECTION:
    '-sV'                 => FILTER_VALIDATE_BOOLEAN,
    '--version-light'     => FILTER_VALIDATE_BOOLEAN,
    '--version-intensity' => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0, 'max_range' => 9]],
    '--version-all'       => FILTER_VALIDATE_BOOLEAN,
    '--version-trace'     => FILTER_VALIDATE_BOOLEAN,
// SCRIPT SCAN:
    '-sC'           => FILTER_VALIDATE_BOOLEAN,
    '--script'      => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[a-z][a-z0-9,\-\.\/]*$/']],
    '--script-args' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^([a-zA-Z][a-zA-Z0-9\-_]*=[^"]+(,[a-zA-Z][a-zA-Z0-9\-_]*=[^"]+)?)$/']],
// OS DETECTION:
    '-O'             => FILTER_VALIDATE_BOOLEAN,
    '--osscan-limit' => FILTER_VALIDATE_BOOLEAN,
    '--osscan-guess' => FILTER_VALIDATE_BOOLEAN,
    '--max-os-tries' => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
// TIMING AND PERFORMANCE:
    '-T'                    => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0, 'max_range' => 5]],
    '--min-hostgroup'       => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--max-hostgroup'       => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--min-parallelism'     => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--max-parallelism'     => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--min-rtt-timeout'     => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
    '--max-rtt-timeout'     => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
    '--initial-rtt-timeout' => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
    '--max-retries'         => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    '--host-timeout'        => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
    '--scan-delay'          => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
    '--max-scan-delay'      => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $tempoRegex]],
// FIREWALL/IDS EVASION AND SPOOFING:
    '-f'            => FILTER_VALIDATE_INT,
    '--mtu'         => FILTER_VALIDATE_INT,
    '-D'            => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $hostsListRegex]],
    '-S'            => ['filter' => FILTER_VALIDATE_IP],
    '-e'            => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^[a-z\d]+$/']],
    '-g'            => FILTER_VALIDATE_INT,
    '--source-port' => FILTER_VALIDATE_INT,
    '--data-length' => FILTER_VALIDATE_INT,
    '--ip-options'  => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => '/^\"(R|T|U|L [\da-zA-Z-.: ]+|S [\da-zA-Z-.: ]+|\\\\x[\da-fA-F]{1,2}(\*[\d]+)?|\\\\[0-2]?[\d]{1,2}(\*[\d]+)?)\"$/']],
    '--ttl'         => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0, 'max_range' => 255]],
    '--spoof-mac'   => FILTER_VALIDATE_MAC,
    '--badsum'      => FILTER_VALIDATE_BOOLEAN,
// MISC:
        // '6' => FILTER_VALIDATE_BOOLEAN,
    '-A'             => FILTER_VALIDATE_BOOLEAN,
    '--send-eth'     => FILTER_VALIDATE_BOOLEAN,
    '--privileged'   => FILTER_VALIDATE_BOOLEAN,
    '-V'             => FILTER_VALIDATE_BOOLEAN,
    '--unprivileged' => FILTER_VALIDATE_BOOLEAN,
    '-h'             => FILTER_VALIDATE_BOOLEAN,
    '--stylesheet'   => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $fileNameRegex]],
// lanScan
    'name'        => ['filter' => FILTER_VALIDATE_REGEXP, 'options' => ['regexp' => $fileNameRegex]],
    'originalURL'   => FILTER_VALIDATE_URL,
    'refreshPeriod' => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 0]],
    'sudo'          => FILTER_VALIDATE_BOOLEAN,
  ], false) ?: $presets["default"];
}

$options["--datadir"] = $DATADIR;
$options["--script-args-file"] = $SCRIPTARGS;

/*echo "<!--";
var_dump($options);
echo "-->\n";*/
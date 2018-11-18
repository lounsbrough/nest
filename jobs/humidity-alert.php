<?PHP
    require_once dirname(__FILE__).'/../classes/pushbullet.php';
    $pushbullet = new Pushbullet();

    require_once dirname(__FILE__).'/../classes/location.php';
    $location = new Location();

    $locationPresence = $location->getPresence();
    if (empty($locationPresence["home"])) {
        return;
    }

    // Only run check between November and February
    if (in_array(date("n"), array(11, 12, 1, 2), true)) {
        return;
    }

    // Run at 7 am and 5 pm
    if (!((int) date("H") == 7 || (int) date("H") == 17)) {
        return;
    }

    $humidityWarningLimits = array(
        "minimum" => 35,
        "maximum" => 50
    );

    $lastAlertTimestamps = json_decode(file_get_contents(dirname(__FILE__)."/../control-files/last-alert-timestamps.json"), true);
    if (abs(time() - strtotime($lastAlertTimestamps["humidityAlert"])) > 2 * 3600) {
        $lastAlertTimestamps["humidityAlert"] = date("Y-m-d H:i:s");
        file_put_contents(dirname(__FILE__)."/../control-files/last-alert-timestamps.json", json_encode($lastAlertTimestamps));

        require_once dirname(__FILE__).'/../classes/nest.class.php';
        $nest = new Nest(getenv('NEST_API_USERNAME'), getenv('NEST_API_PASSWORD'));

        $nestInfo = $nest->getDeviceInfo();
        $currentHumidity = $nestInfo->current_state->humidity;
        $currentTemperature = $nestInfo->current_state->temperature;

        if ($currentHumidity < $humidityWarningLimits["minimum"] || $currentHumidity > $humidityWarningLimits["maximum"]) {
            foreach ($locationPresence["home"] as $person) {
                $deviceName = ucwords(strtolower($person))." - Phone";
                $noteBody = "Temperature: ".round($currentTemperature, 1)."° Humidity: $currentHumidity%";
                $pushbullet->pushNote($deviceName, "Humidity Alert", $noteBody);
            }
        }
    }
?>
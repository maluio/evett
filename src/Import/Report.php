<?php


namespace App\Import;


use Carbon\Carbon;

class Report
{
    /**
     * @var Carbon
     */
    public $day;

    /**
     * @var ProviderReport[]
     */
    private $providerReports = [];

    public function addProviderReport(ProviderReport $providerReport): void{
        $this->providerReports[] = $providerReport;
    }

    public function getMessage(): string {
        dump($this->providerReports);
        $message = '';
        foreach ($this->providerReports as $provider){
            $message = $provider->numberOfFoundEvents . ' events found, ';
            $message .= $provider->numberOfImportedEvents . ' imported';
            $message .= 'for ' . $provider->key . PHP_EOL;
        }

        $message .= ' for ' . $this->day->toDayDateTimeString();

        return $message;
    }
}
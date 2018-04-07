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
        $message = 'Event import for ' . $this->day->format('D, d M') . PHP_EOL;
        $numberOfImports = 0;
        foreach ($this->providerReports as $provider){
            //$message = $provider->numberOfFoundEvents . ' events found, ';
            if($provider->numberOfImportedEvents > 0){
                $message .= $provider->numberOfImportedEvents . ' imported ';
                $message .= 'for ' . $provider->key . PHP_EOL;
                $numberOfImports =+ $provider->numberOfImportedEvents;
            }
        }
        $this->providerReports = [];

        if(0 === $numberOfImports){
            return '';
        }

        return $message;
    }
}
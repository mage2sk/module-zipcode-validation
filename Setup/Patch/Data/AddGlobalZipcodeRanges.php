<?php
declare(strict_types=1);

namespace Panth\ZipcodeValidation\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddGlobalZipcodeRanges implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('panth_zipcode_range');

        // Skip if table already has data
        if ((int) $connection->fetchOne("SELECT COUNT(*) FROM {$table}") > 0) {
            $this->moduleDataSetup->endSetup();
            return $this;
        }

        $ranges = array_merge(
            $this->getIndiaRanges(),
            $this->getUSRanges(),
            $this->getUKRanges(),
            $this->getCanadaRanges(),
            $this->getAustraliaRanges(),
            $this->getEuropeRanges()
        );

        foreach (array_chunk($ranges, 50) as $chunk) {
            $connection->insertMultiple($table, $chunk);
        }

        $this->moduleDataSetup->endSetup();
        return $this;
    }

    private function getIndiaRanges(): array
    {
        return [
            ['country_id' => 'IN', 'state_code' => 'AN', 'state_name' => 'Andaman and Nicobar', 'zip_start' => '744101', 'zip_end' => '744301', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'AP', 'state_name' => 'Andhra Pradesh', 'zip_start' => '515001', 'zip_end' => '535594', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'AR', 'state_name' => 'Arunachal Pradesh', 'zip_start' => '790001', 'zip_end' => '792131', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'AS', 'state_name' => 'Assam', 'zip_start' => '781001', 'zip_end' => '788931', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'BR', 'state_name' => 'Bihar', 'zip_start' => '800001', 'zip_end' => '855117', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'CH', 'state_name' => 'Chandigarh', 'zip_start' => '160001', 'zip_end' => '160101', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'CT', 'state_name' => 'Chhattisgarh', 'zip_start' => '490001', 'zip_end' => '497778', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'DL', 'state_name' => 'Delhi', 'zip_start' => '110001', 'zip_end' => '110097', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'GA', 'state_name' => 'Goa', 'zip_start' => '403001', 'zip_end' => '403806', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'GJ', 'state_name' => 'Gujarat', 'zip_start' => '360001', 'zip_end' => '396590', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'HR', 'state_name' => 'Haryana', 'zip_start' => '121001', 'zip_end' => '136156', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'HP', 'state_name' => 'Himachal Pradesh', 'zip_start' => '171001', 'zip_end' => '177601', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'JK', 'state_name' => 'Jammu & Kashmir', 'zip_start' => '180001', 'zip_end' => '194404', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'JH', 'state_name' => 'Jharkhand', 'zip_start' => '813101', 'zip_end' => '835325', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'KA', 'state_name' => 'Karnataka', 'zip_start' => '560001', 'zip_end' => '591346', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'KL', 'state_name' => 'Kerala', 'zip_start' => '670001', 'zip_end' => '695615', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'LA', 'state_name' => 'Ladakh', 'zip_start' => '194101', 'zip_end' => '194401', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'MP', 'state_name' => 'Madhya Pradesh', 'zip_start' => '450001', 'zip_end' => '488448', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'MH', 'state_name' => 'Maharashtra', 'zip_start' => '400001', 'zip_end' => '444999', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'MN', 'state_name' => 'Manipur', 'zip_start' => '795001', 'zip_end' => '795159', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'ML', 'state_name' => 'Meghalaya', 'zip_start' => '793001', 'zip_end' => '794115', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'MZ', 'state_name' => 'Mizoram', 'zip_start' => '796001', 'zip_end' => '796901', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'NL', 'state_name' => 'Nagaland', 'zip_start' => '797001', 'zip_end' => '798627', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'OR', 'state_name' => 'Odisha', 'zip_start' => '751001', 'zip_end' => '770076', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'PY', 'state_name' => 'Puducherry', 'zip_start' => '605001', 'zip_end' => '609607', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'PB', 'state_name' => 'Punjab', 'zip_start' => '140001', 'zip_end' => '152123', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'RJ', 'state_name' => 'Rajasthan', 'zip_start' => '301001', 'zip_end' => '345034', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'SK', 'state_name' => 'Sikkim', 'zip_start' => '737101', 'zip_end' => '737139', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'TN', 'state_name' => 'Tamil Nadu', 'zip_start' => '600001', 'zip_end' => '643253', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'TG', 'state_name' => 'Telangana', 'zip_start' => '500001', 'zip_end' => '509412', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'TR', 'state_name' => 'Tripura', 'zip_start' => '799001', 'zip_end' => '799290', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'zip_start' => '200001', 'zip_end' => '285223', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'UT', 'state_name' => 'Uttarakhand', 'zip_start' => '244001', 'zip_end' => '263680', 'is_active' => 1],
            ['country_id' => 'IN', 'state_code' => 'WB', 'state_name' => 'West Bengal', 'zip_start' => '700001', 'zip_end' => '743711', 'is_active' => 1],
        ];
    }

    private function getUSRanges(): array
    {
        return [
            ['country_id' => 'US', 'state_code' => 'AL', 'state_name' => 'Alabama', 'zip_start' => '35004', 'zip_end' => '36925', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'AK', 'state_name' => 'Alaska', 'zip_start' => '99501', 'zip_end' => '99950', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'AZ', 'state_name' => 'Arizona', 'zip_start' => '85001', 'zip_end' => '86556', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'CA', 'state_name' => 'California', 'zip_start' => '90001', 'zip_end' => '96162', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'CO', 'state_name' => 'Colorado', 'zip_start' => '80001', 'zip_end' => '81658', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'FL', 'state_name' => 'Florida', 'zip_start' => '32003', 'zip_end' => '34997', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'GA', 'state_name' => 'Georgia', 'zip_start' => '30002', 'zip_end' => '31999', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'IL', 'state_name' => 'Illinois', 'zip_start' => '60001', 'zip_end' => '62999', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'NY', 'state_name' => 'New York', 'zip_start' => '10001', 'zip_end' => '14975', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'TX', 'state_name' => 'Texas', 'zip_start' => '73301', 'zip_end' => '79999', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'WA', 'state_name' => 'Washington', 'zip_start' => '98001', 'zip_end' => '99403', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'MA', 'state_name' => 'Massachusetts', 'zip_start' => '01001', 'zip_end' => '02791', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'PA', 'state_name' => 'Pennsylvania', 'zip_start' => '15001', 'zip_end' => '19640', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'OH', 'state_name' => 'Ohio', 'zip_start' => '43001', 'zip_end' => '45999', 'is_active' => 1],
            ['country_id' => 'US', 'state_code' => 'NJ', 'state_name' => 'New Jersey', 'zip_start' => '07001', 'zip_end' => '08989', 'is_active' => 1],
        ];
    }

    private function getUKRanges(): array
    {
        return [
            ['country_id' => 'GB', 'state_code' => 'LDN', 'state_name' => 'London', 'zip_start' => 'E1', 'zip_end' => 'W14', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'MAN', 'state_name' => 'Manchester', 'zip_start' => 'M1', 'zip_end' => 'M90', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'BHM', 'state_name' => 'Birmingham', 'zip_start' => 'B1', 'zip_end' => 'B97', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'LDS', 'state_name' => 'Leeds', 'zip_start' => 'LS1', 'zip_end' => 'LS29', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'EDI', 'state_name' => 'Edinburgh', 'zip_start' => 'EH1', 'zip_end' => 'EH54', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'GLA', 'state_name' => 'Glasgow', 'zip_start' => 'G1', 'zip_end' => 'G84', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'BRS', 'state_name' => 'Bristol', 'zip_start' => 'BS1', 'zip_end' => 'BS49', 'is_active' => 1],
            ['country_id' => 'GB', 'state_code' => 'LIV', 'state_name' => 'Liverpool', 'zip_start' => 'L1', 'zip_end' => 'L75', 'is_active' => 1],
        ];
    }

    private function getCanadaRanges(): array
    {
        return [
            ['country_id' => 'CA', 'state_code' => 'ON', 'state_name' => 'Ontario', 'zip_start' => 'K0A', 'zip_end' => 'P9N', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'QC', 'state_name' => 'Quebec', 'zip_start' => 'G0A', 'zip_end' => 'J9Z', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'BC', 'state_name' => 'British Columbia', 'zip_start' => 'V0A', 'zip_end' => 'V9Z', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'AB', 'state_name' => 'Alberta', 'zip_start' => 'T0A', 'zip_end' => 'T9Z', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'MB', 'state_name' => 'Manitoba', 'zip_start' => 'R0A', 'zip_end' => 'R9A', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'SK', 'state_name' => 'Saskatchewan', 'zip_start' => 'S0A', 'zip_end' => 'S9Z', 'is_active' => 1],
            ['country_id' => 'CA', 'state_code' => 'NS', 'state_name' => 'Nova Scotia', 'zip_start' => 'B0A', 'zip_end' => 'B9A', 'is_active' => 1],
        ];
    }

    private function getAustraliaRanges(): array
    {
        return [
            ['country_id' => 'AU', 'state_code' => 'NSW', 'state_name' => 'New South Wales', 'zip_start' => '2000', 'zip_end' => '2999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'VIC', 'state_name' => 'Victoria', 'zip_start' => '3000', 'zip_end' => '3999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'QLD', 'state_name' => 'Queensland', 'zip_start' => '4000', 'zip_end' => '4999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'SA', 'state_name' => 'South Australia', 'zip_start' => '5000', 'zip_end' => '5999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'WA', 'state_name' => 'Western Australia', 'zip_start' => '6000', 'zip_end' => '6999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'TAS', 'state_name' => 'Tasmania', 'zip_start' => '7000', 'zip_end' => '7999', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'NT', 'state_name' => 'Northern Territory', 'zip_start' => '0800', 'zip_end' => '0899', 'is_active' => 1],
            ['country_id' => 'AU', 'state_code' => 'ACT', 'state_name' => 'Australian Capital Territory', 'zip_start' => '2600', 'zip_end' => '2620', 'is_active' => 1],
        ];
    }

    private function getEuropeRanges(): array
    {
        return [
            ['country_id' => 'DE', 'state_code' => 'BE', 'state_name' => 'Berlin', 'zip_start' => '10115', 'zip_end' => '14199', 'is_active' => 1],
            ['country_id' => 'DE', 'state_code' => 'BY', 'state_name' => 'Bavaria', 'zip_start' => '80331', 'zip_end' => '97999', 'is_active' => 1],
            ['country_id' => 'DE', 'state_code' => 'HH', 'state_name' => 'Hamburg', 'zip_start' => '20095', 'zip_end' => '22769', 'is_active' => 1],
            ['country_id' => 'FR', 'state_code' => '75', 'state_name' => 'Paris', 'zip_start' => '75001', 'zip_end' => '75020', 'is_active' => 1],
            ['country_id' => 'FR', 'state_code' => '13', 'state_name' => 'Marseille', 'zip_start' => '13001', 'zip_end' => '13016', 'is_active' => 1],
            ['country_id' => 'FR', 'state_code' => '69', 'state_name' => 'Lyon', 'zip_start' => '69001', 'zip_end' => '69009', 'is_active' => 1],
            ['country_id' => 'IT', 'state_code' => 'RM', 'state_name' => 'Rome', 'zip_start' => '00100', 'zip_end' => '00199', 'is_active' => 1],
            ['country_id' => 'IT', 'state_code' => 'MI', 'state_name' => 'Milan', 'zip_start' => '20100', 'zip_end' => '20199', 'is_active' => 1],
            ['country_id' => 'ES', 'state_code' => 'M', 'state_name' => 'Madrid', 'zip_start' => '28001', 'zip_end' => '28080', 'is_active' => 1],
            ['country_id' => 'ES', 'state_code' => 'B', 'state_name' => 'Barcelona', 'zip_start' => '08001', 'zip_end' => '08042', 'is_active' => 1],
            ['country_id' => 'NL', 'state_code' => 'NH', 'state_name' => 'Amsterdam', 'zip_start' => '1011', 'zip_end' => '1109', 'is_active' => 1],
        ];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}

<?php
/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

namespace Panth\ZipcodeValidation\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Serialize\Serializer\Json;

class AddIndianPincodeRanges implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param WriterInterface $configWriter
     * @param Json $serializer
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        WriterInterface $configWriter,
        Json $serializer
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configWriter = $configWriter;
        $this->serializer = $serializer;
    }

    /**
     * Apply patch
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // Define Indian PIN code ranges for all 38 states and union territories
        $indianRanges = [
            ['country_id' => 'IN', 'state_code' => 'AN', 'state_name' => 'Andaman and Nicobar Islands', 'pincode_start' => '744101', 'pincode_end' => '744301'],
            ['country_id' => 'IN', 'state_code' => 'AP', 'state_name' => 'Andhra Pradesh', 'pincode_start' => '515001', 'pincode_end' => '535594'],
            ['country_id' => 'IN', 'state_code' => 'AR', 'state_name' => 'Arunachal Pradesh', 'pincode_start' => '790001', 'pincode_end' => '792131'],
            ['country_id' => 'IN', 'state_code' => 'AS', 'state_name' => 'Assam', 'pincode_start' => '781001', 'pincode_end' => '788931'],
            ['country_id' => 'IN', 'state_code' => 'BR', 'state_name' => 'Bihar', 'pincode_start' => '800001', 'pincode_end' => '855117'],
            ['country_id' => 'IN', 'state_code' => 'CH', 'state_name' => 'Chandigarh', 'pincode_start' => '160001', 'pincode_end' => '160103'],
            ['country_id' => 'IN', 'state_code' => 'CT', 'state_name' => 'Chhattisgarh', 'pincode_start' => '490001', 'pincode_end' => '497778'],
            ['country_id' => 'IN', 'state_code' => 'DL', 'state_name' => 'Delhi', 'pincode_start' => '110001', 'pincode_end' => '110097'],
            ['country_id' => 'IN', 'state_code' => 'DN', 'state_name' => 'Dadra and Nagar Haveli', 'pincode_start' => '396193', 'pincode_end' => '396240'],
            ['country_id' => 'IN', 'state_code' => 'DD', 'state_name' => 'Daman and Diu', 'pincode_start' => '362510', 'pincode_end' => '362579'],
            ['country_id' => 'IN', 'state_code' => 'GA', 'state_name' => 'Goa', 'pincode_start' => '403001', 'pincode_end' => '403806'],
            ['country_id' => 'IN', 'state_code' => 'GJ', 'state_name' => 'Gujarat', 'pincode_start' => '360001', 'pincode_end' => '396590'],
            ['country_id' => 'IN', 'state_code' => 'HR', 'state_name' => 'Haryana', 'pincode_start' => '121001', 'pincode_end' => '136156'],
            ['country_id' => 'IN', 'state_code' => 'HP', 'state_name' => 'Himachal Pradesh', 'pincode_start' => '171001', 'pincode_end' => '177601'],
            ['country_id' => 'IN', 'state_code' => 'JK', 'state_name' => 'Jammu and Kashmir', 'pincode_start' => '180001', 'pincode_end' => '194404'],
            ['country_id' => 'IN', 'state_code' => 'JH', 'state_name' => 'Jharkhand', 'pincode_start' => '814101', 'pincode_end' => '835325'],
            ['country_id' => 'IN', 'state_code' => 'KA', 'state_name' => 'Karnataka', 'pincode_start' => '560001', 'pincode_end' => '591346'],
            ['country_id' => 'IN', 'state_code' => 'KL', 'state_name' => 'Kerala', 'pincode_start' => '670001', 'pincode_end' => '695615'],
            ['country_id' => 'IN', 'state_code' => 'LA', 'state_name' => 'Ladakh', 'pincode_start' => '194101', 'pincode_end' => '194404'],
            ['country_id' => 'IN', 'state_code' => 'LD', 'state_name' => 'Lakshadweep', 'pincode_start' => '682551', 'pincode_end' => '682559'],
            ['country_id' => 'IN', 'state_code' => 'MP', 'state_name' => 'Madhya Pradesh', 'pincode_start' => '450001', 'pincode_end' => '488448'],
            ['country_id' => 'IN', 'state_code' => 'MH', 'state_name' => 'Maharashtra', 'pincode_start' => '400001', 'pincode_end' => '445402'],
            ['country_id' => 'IN', 'state_code' => 'MN', 'state_name' => 'Manipur', 'pincode_start' => '795001', 'pincode_end' => '795159'],
            ['country_id' => 'IN', 'state_code' => 'ML', 'state_name' => 'Meghalaya', 'pincode_start' => '793001', 'pincode_end' => '794115'],
            ['country_id' => 'IN', 'state_code' => 'MZ', 'state_name' => 'Mizoram', 'pincode_start' => '796001', 'pincode_end' => '796901'],
            ['country_id' => 'IN', 'state_code' => 'NL', 'state_name' => 'Nagaland', 'pincode_start' => '797001', 'pincode_end' => '798627'],
            ['country_id' => 'IN', 'state_code' => 'OR', 'state_name' => 'Odisha', 'pincode_start' => '751001', 'pincode_end' => '770076'],
            ['country_id' => 'IN', 'state_code' => 'PY', 'state_name' => 'Puducherry', 'pincode_start' => '605001', 'pincode_end' => '609609'],
            ['country_id' => 'IN', 'state_code' => 'PB', 'state_name' => 'Punjab', 'pincode_start' => '140001', 'pincode_end' => '160104'],
            ['country_id' => 'IN', 'state_code' => 'RJ', 'state_name' => 'Rajasthan', 'pincode_start' => '301001', 'pincode_end' => '345034'],
            ['country_id' => 'IN', 'state_code' => 'SK', 'state_name' => 'Sikkim', 'pincode_start' => '737101', 'pincode_end' => '737139'],
            ['country_id' => 'IN', 'state_code' => 'TN', 'state_name' => 'Tamil Nadu', 'pincode_start' => '600001', 'pincode_end' => '643253'],
            ['country_id' => 'IN', 'state_code' => 'TG', 'state_name' => 'Telangana', 'pincode_start' => '500001', 'pincode_end' => '509412'],
            ['country_id' => 'IN', 'state_code' => 'TR', 'state_name' => 'Tripura', 'pincode_start' => '799001', 'pincode_end' => '799290'],
            ['country_id' => 'IN', 'state_code' => 'UP', 'state_name' => 'Uttar Pradesh', 'pincode_start' => '201001', 'pincode_end' => '285223'],
            ['country_id' => 'IN', 'state_code' => 'UT', 'state_name' => 'Uttarakhand', 'pincode_start' => '244001', 'pincode_end' => '263680'],
            ['country_id' => 'IN', 'state_code' => 'WB', 'state_name' => 'West Bengal', 'pincode_start' => '700001', 'pincode_end' => '743711']
        ];

        // Serialize and save to configuration
        $serializedData = $this->serializer->serialize($indianRanges);

        $this->configWriter->save(
            'zipcode_validation/pincode_ranges/custom_ranges',
            $serializedData
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get aliases
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }
}

<?php
namespace Bookly\Backend\Components\Dialogs\Staff\Edit\Forms;

use Bookly\Lib;

class StaffServices extends Lib\Base\Form
{
    protected static $entity_class = 'StaffService';

    /** @var Lib\Entities\Category[] */
    private $categories = array();

    /** @var array */
    private $services_data = array();

    /** @var array */
    private $uncategorized_services = array();

    public function configure()
    {
        $this->setFields( array( 'price', 'deposit', 'service', 'staff_id', 'location_id', 'capacity_min', 'capacity_max', 'custom_services' ) );
    }

    public function load( $staff_id, $location_id = null )
    {
        $types = array( Lib\Entities\Service::TYPE_SIMPLE );
        if ( Lib\Config::packagesActive() ) {
            $types[] = Lib\Entities\Service::TYPE_PACKAGE;
        }
        $data = Lib\Entities\Category::query( 'c' )
            ->select( 'c.name AS category_name, s.*' )
            ->innerJoin( 'Service', 's', 's.category_id = c.id' )
            ->sortBy( 'c.position, s.position' )
            ->whereIn( 's.type', $types )
            ->fetchArray();
        if ( ! $data ) {
            $data = array();
        }

        $this->uncategorized_services = Lib\Entities\Service::query( 's' )
            ->where( 's.category_id', null )
            ->whereIn( 's.type', $types )
            ->sortBy( 's.position' )
            ->find();

        $staff_services = Lib\Entities\StaffService::query( 'ss' )
            ->select( 'ss.service_id, ss.price, ss.deposit, ss.capacity_min, ss.capacity_max' )
            ->where( 'ss.staff_id', $staff_id )
            ->where( 'ss.location_id', $location_id )
            ->fetchArray();
        if ( $staff_services ) {
            foreach ( $staff_services as $staff_service ) {
                $this->services_data[ $staff_service['service_id'] ] = array( 'price' => $staff_service['price'], 'deposit' => $staff_service['deposit'], 'capacity_min' => $staff_service['capacity_min'], 'capacity_max' => $staff_service['capacity_max'] );
            }
        }

        foreach ( $data as $row ) {
            if ( ! isset( $this->categories[ $row['category_id'] ] ) ) {
                $category = new Lib\Entities\Category( array( 'id' => $row['category_id'], 'name' => $row['category_name'] ) );
                $this->categories[ $row['category_id'] ] = $category;
            }
            unset( $row['category_name'] );

            $service = new Lib\Entities\Service( $row );
            $this->categories[ $row['category_id'] ]->addService( $service );
        }

    }

    public function save()
    {
        $staff_id    = $this->data['staff_id'];
        $location_id = array_key_exists( 'location_id', $this->data ) && $this->data['location_id'] ? $this->data['location_id'] : null;
        if ( $staff_id ) {
            Lib\Entities\StaffService::query()
                ->delete()
                ->where( 'staff_id', $staff_id )
                ->where( 'location_id', $location_id )
                ->whereNotIn( 'service_id', array_key_exists( 'service', $this->data ) ? (array) $this->data['service'] : array() )
                ->execute();
            if ( isset ( $this->data['service'] ) && ( ! isset ( $this->data['custom_services'] ) || $this->data['custom_services'] == '1' ) ) {
                foreach ( $this->data['service'] as $service_id ) {
                    $staff_service = new Lib\Entities\StaffService();
                    $staff_service->loadBy( compact( 'staff_id', 'service_id', 'location_id' ) );
                    if ( isset( $this->data['capacity_min'][ $service_id ] ) ) {
                        $staff_service->setCapacityMin( $this->data['capacity_min'][ $service_id ] );
                    }
                    if ( isset( $this->data['capacity_max'][ $service_id ] ) ) {
                        $staff_service->setCapacityMax( $this->data['capacity_max'][ $service_id ] );
                    }
                    if ( isset( $this->data['deposit'][ $service_id ] ) ) {
                        $staff_service->setDeposit( preg_replace( '/[^0-9%.]/', '', str_replace( ',', '.', $this->data['deposit'][ $service_id ] ) ) );
                    }
                    $staff_service
                        ->setPrice( $this->data['price'][ $service_id ] )
                        ->setServiceId( $service_id )
                        ->setStaffId( $staff_id )
                        ->setLocationId( $location_id )
                        ->save();
                }
            }
        }
    }

    /**
     * @return Lib\Entities\Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getServicesData()
    {
        return $this->services_data;
    }

    /**
     * @return Lib\Entities\Service[]
     */
    public function getUncategorizedServices()
    {
        return $this->uncategorized_services;
    }

}
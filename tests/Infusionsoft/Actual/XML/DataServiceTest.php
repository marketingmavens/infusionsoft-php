<?php

namespace Infusionsoft\Actual\XML;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;

class DataServiceTest extends TestCase
{

    /** @var Infusionsoft */
    protected static $infusionsoft;

    /**
     * @throws InfusionsoftException
     */
    public static function setUpBeforeClass(): void
    {
        require_once('./samples/helpers.php');
        self::$infusionsoft = set_up_and_get_infusionsoft();
    }

    /**
     * @return Collection
     * @throws InfusionsoftException
     */
    public function testDSQueryReturnsIlluminateCollection()
    {
        $contacts = self::$infusionsoft->data()
            ->query('Contact', 1, 0, ['Id' => '%'], ['Id', 'FirstName', 'LastName'], 'Id', true);

        $this->assertTrue(Collection::class == get_class($contacts));

        return $contacts;
    }

    /**
     * @depends testDSQueryReturnsIlluminateCollection
     *
     * @param $contacts
     */
    public function testDSQueryCollectionCanConvertToArray($contacts)
    {
        $this->assertTrue(is_array($contacts->toArray()));
    }

    public function testDSCanCount()
    {
        $count = self::$infusionsoft->data()->count('Contact', ['Id' => '%']);

        $this->assertTrue($count > 1, 'There is more then 1 contact id database');

        return $count;
    }

    /**
     * @depends testDSCanCount
     *
     * @param $amount
     */
    public function testDSCountIsInteger($amount)
    {
        $this->assertTrue(is_int($amount), 'Count returned is an integer');
    }

    /**
     * @return Collection|mixed
     * @throws InfusionsoftException
     */
    public function testDSAddReturnsIdAndData()
    {
        $contactData = [
            'FirstName' => 'PHPUnit',
            'LastName'  => 'DS-Add'
        ];

        $contact = self::$infusionsoft->data()->add('Contact', $contactData);

        $this->assertArrayHasKey('Id', $contact);
        $this->assertArrayHasKey('FirstName', $contact);
        $this->assertArrayHasKey('LastName', $contact);

        $this->assertEquals($contactData['FirstName'], $contact['FirstName']);
        $this->assertEquals($contactData['LastName'], $contact['LastName']);

        return $contact;
    }

    /**
     * @depends testDSAddReturnsIdAndData
     *
     * @param $contact
     */
    public function testDSUpdateReturnsIdAndData($contact)
    {
        $contactId = $contact['Id'];
        $contactData = [
            'FirstName' => 'PHPUnit',
            'LastName'  => 'DS-Update'
        ];

        $contact = self::$infusionsoft->data()->update('Contact', $contactId, $contactData);

        $this->assertArrayHasKey('Id', $contact);
        $this->assertArrayHasKey('FirstName', $contact);
        $this->assertArrayHasKey('LastName', $contact);

        $this->assertEquals($contactId, $contact['Id']);
        $this->assertEquals($contactData['FirstName'], $contact['FirstName']);
        $this->assertEquals($contactData['LastName'], $contact['LastName']);

        //Delete Contact
        self::$infusionsoft->contacts()->find($contact['Id'])->delete();
    }
}

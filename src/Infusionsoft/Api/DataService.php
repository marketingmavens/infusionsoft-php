<?php

namespace Infusionsoft\Api;

use Illuminate\Support\Collection;
use Infusionsoft\InfusionsoftException;

class DataService extends AbstractApi
{

    /**
     * @param $table
     * @param $values
     *
     * @return \Illuminate\Support\Collection|mixed
     * @throws \Infusionsoft\InfusionsoftException
     */
    public function add($table, $values)
    {
        $response = $this->client->request('DataService.add', $table, $values);

        if (is_int($response)) {
            $values['Id'] = $response;

            return $values;
        }

        return $response;
    }

    /**
     * @param string  $table
     * @param integer $recordId
     * @param array   $wantedFields
     *
     * @return array
     */
    public function load($table, $recordId, $wantedFields)
    {
        return $this->client->request('DataService.load', $table, $recordId, $wantedFields);
    }

    /**
     * @param string  $table
     * @param integer $id
     * @param array   $values
     *
     * @return array
     */
    public function update($table, $id, $values)
    {
        $response = $this->client->request('DataService.update', $table, $id, $values);

        if (is_int($response)) {
            $values['Id'] = $response;

            return $values;
        }

        return $response;
    }

    /**
     * @param string  $table
     * @param integer $id
     *
     * @return bool
     */
    public function delete($table, $id)
    {
        return $this->client->request('DataService.delete', $table, $id);
    }

    /**
     * @param string  $table
     * @param integer $limit
     * @param integer $page
     * @param string  $fieldName
     * @param string  $fieldValue
     * @param array   $returnFields
     *
     * @return array
     */
    public function findByField($table, $limit, $page, $fieldName, $fieldValue, $returnFields)
    {
        return $this->client->request('DataService.findByField', $table, $limit, $page, $fieldName,
            $fieldValue, $returnFields);
    }

    /**
     * @param string  $table
     * @param integer $limit
     * @param integer $page
     * @param array   $queryData
     * @param array   $selectedFields
     * @param string  $orderBy
     * @param boolean $ascending
     *
     * @return Collection
     */
    public function query(
        $table,
        $limit,
        $page,
        $queryData,
        $selectedFields,
        $orderBy = 'Id',
        $ascending = true
    ) {
        if ($table === 'ContactGroupAssign' && $orderBy === 'Id') {
            $orderBy = 'DateCreated';
        }

        $response = $this->client->request('DataService.query', $table, $limit, $page, $queryData,
            $selectedFields, $orderBy, $ascending);

        if ( ! is_array($response)) {
            throw new InfusionsoftException('Error getting query: ' . (string)$response);
        }

        return new Collection($response);
    }

    /**
     * @param string $table
     * @param array  $queryData
     *
     * @return integer
     */
    public function count($table, $queryData)
    {
        return $this->client->request('DataService.count', $table, $queryData);
    }

    /**
     * @param string  $customFieldType
     * @param string  $displayName
     * @param string  $dataType
     * @param integer $headerId
     *
     * @return integer
     */
    public function addCustomField($customFieldType, $displayName, $dataType, $headerId)
    {
        return $this->client->request('DataService.addCustomField', $customFieldType, $displayName,
            $dataType, $headerId);
    }

    /**
     * @param string $username
     * @param string $passwordHash
     *
     * @return integer
     */
    public function authenticateUser($username, $passwordHash)
    {
        return $this->client->request('DataService.authenticateUser', $username, $passwordHash);
    }

    /**
     * @param string $module
     * @param string $setting
     *
     * @return string
     */
    public function getAppSetting($module, $setting)
    {
        return $this->client->request('DataService.getAppSetting', $module, $setting);
    }

    /**
     * @param integer $appointmentId
     *
     * @return string
     */
    public function getAppointmentCal($appointmentId)
    {
        return $this->client->request('DataService.getAppointmentCal', $appointmentId);
    }

    /**
     * @param string $username
     * @param string $passwordHash
     *
     * @return string
     */
    public function getTemporaryKey($username, $passwordHash)
    {
        return $this->client->request('DataService.getTemporaryKey', $username, $passwordHash);
    }

    /**
     * @param integer $customFieldId
     * @param array   $values
     *
     * @return bool
     */
    public function updateCustomField($customFieldId, $values)
    {
        return $this->client->request('DataService.updateCustomField', $customFieldId, $values);
    }

    /**
     * @return array
     */
    public function getUserInfo()
    {
        $this->client->needsEmptyKey = false;

        return $this->client->request('DataService.getUserInfo');
    }

}

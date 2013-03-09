<?php namespace Admeris;

use Admeris\Base;

class PeriodicPurchaseInfo extends Base {

  private $customerId;
  private $endDate;
  private $lastPaymentId;
  private $nextPaymentDate;
  private $orderId;
  private $periodicTransactionId;
  private $perPaymentAmount;
  private $schedule;
  private $startDate;
  private $state;

  public function _get_customerId() {return $this->customerId;}
  public function _get_endDate() {return $this->endDate;}
  public function _get_lastPaymentId() {return $this->lastPaymentId;}
  public function _get_nextPaymentDate() {return $this->nextPaymentDate;}
  public function _get_orderId() {return $this->orderId;}
  public function _get_periodicTransactionId() {return $this->periodicTransactionId;}
  public function _get_perPaymentAmount() {return $this->perPaymentAmount;}
  public function _get_schedule() {return $this->schedule;}
  public function _get_startDate() {return $this->startDate;}
  public function _get_state() {return $this->state;}

  public function _set_customerId($data) {$this->customerId = $data;}
  public function _set_endDate($data) {$this->endDate = $data;}
  public function _set_lastPaymentId($data) {$this->lastPaymentId = $data;}
  public function _set_nextPaymentDate($data) {$this->nextPaymentDate = $data;}
  public function _set_orderId($data) {$this->orderId = $data;}
  public function _set_periodicTransactionId($data) {$this->periodicTransactionId = $data;}
  public function _set_perPaymentAmount($data) {$this->perPaymentAmount = $data;}
  public function _set_schedule($data) {$this->schedule = $data;}
  public function _set_startDate($data) {$this->startDate = $data;}
  public function _set_state($data) {$this->state = $data;}
}

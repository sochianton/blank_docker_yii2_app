<?php

namespace common\service;

use common\dto\TransactionDto;
use common\models\Bid;
use common\repository\BidRepository;
use common\repository\BidWorkRepository;
use common\repository\CompanyRepository;
use common\repository\CustomerRepository;
use common\repository\EmployeeRepository;
use common\repository\WorkRepository;
use Yii;

/**
 * Class TransactionService
 * @package common\service
 */
class TransactionService
{
    const MONTH_UNIX = 60 * 60 * 24 * 30; // 2592000
    /**
     * @var BidRepository
     */
    private $bidRepository;
    /**
     * @var BidWorkRepository
     */
    private $bidWorkRepository;
    /**
     * @var WorkRepository
     */
    private $workRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * TransactionService constructor.
     * @param BidRepository $bidRepository
     * @param BidWorkRepository $bidWorkRepository
     * @param WorkRepository $workRepository
     * @param CustomerRepository $customerRepository
     * @param EmployeeRepository $employeeRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        BidRepository $bidRepository,
        BidWorkRepository $bidWorkRepository,
        WorkRepository $workRepository,
        CustomerRepository $customerRepository,
        EmployeeRepository $employeeRepository,
        CompanyRepository $companyRepository
    ) {

        $this->bidRepository = $bidRepository;
        $this->bidWorkRepository = $bidWorkRepository;
        $this->workRepository = $workRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeRepository = $employeeRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param int|null $startDate
     * @param int|null $endDate
     * @param int|null $customerId
     * @param string|null $customerName
     * @param string|null $employeeName
     * @param int|null $price
     * @param int|null $commission
     * @param int|null $bidId
     * @return array
     */
    public function getList(
        ?int $startDate,
        ?int $endDate,
        ?int $customerId = null,
        ?string $customerName = null,
        ?string $employeeName = null,
        ?int $price = null,
        ?int $commission = null,
        ?int $bidId = null
    ): array {
        $bids = [];
        if ($bidId) {
            $bid = $this->bidRepository->get($bidId);
            if ($bid !== null) {
                $bids[] = $bid;
            }
        } else {
            $bids = $this->bidRepository->getListAll($startDate, $endDate, $customerId);
        }
        $transactions = array_map(function (Bid $bid) {
            $customerName = '';
            $employeeName = '';
            $customer = $this->customerRepository->get($bid->customer_id);
            if ($customer !== null) {
                $customerCompany = $this->companyRepository->get($customer->company_id ?? 0);
                $customerName = sprintf('%s (%s)', $customer->getFullName(),
                    $customerCompany->name ?? Yii::t('app', 'company not set'));
            }
            $employee = $this->employeeRepository->get($bid->employee_id ?? 0);
            if ($employee !== null) {
                $employeeCompany = $this->companyRepository->get($employee->company_id ?? 0);
                $employeeName = sprintf('%s (%s)', $employee->getFullName(),
                    $employeeCompany->name ?? Yii::t('app', 'company not set'));
            }
            $commission = 0;
            $workIds = $this->bidWorkRepository->getWorkIds($bid->id);
            if (!empty($workIds)) {
                $workId = array_shift($workIds);
                $work = $this->workRepository->get($workId);
                $commission = $work->commission ?? 0;
            }
            return new TransactionDto(
                $bid->id,
                $bid->complete_at,
                $customerName,
                $employeeName,
                $bid->object,
                $bid->price,
                $commission
            );
        }, $bids);

        return array_filter($transactions,
            function (TransactionDto $transaction) use ($customerName, $employeeName, $price, $commission) {
                if ($customerName !== null && mb_strpos(mb_strtolower($transaction->getCustomer()),
                        mb_strtolower($customerName)) === false) {
                    return false;
                }
                if ($employeeName !== null && mb_strpos(mb_strtolower($transaction->getEmployee()),
                        mb_strtolower($employeeName)) === false) {
                    return false;
                }
                if ($price !== null && $transaction->getPrice() != $price) {
                    return false;
                }
                if ($commission !== null && $transaction->getCommission() != $commission) {
                    return false;
                }
                return true;
            });
    }
}

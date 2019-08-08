<?php

namespace backend\models\forms;

use common\models\Bid;

class BidUpdateForm extends BidCreateForm
{
    /**
     * @param Bid $bid
     * @param array $works
     */
    public function fillFromModel(Bid $bid, array $works = []): void
    {
        $this->id = $bid->id;
        $this->name = $bid->name;
        $this->customerId = $bid->customer_id;
        $this->employeeId = $bid->employee_id;
        $this->status = $bid->status;
        $this->price = $bid->price;
        $this->object = $bid->object;
        $this->customerComment = $bid->customer_comment;
        $this->employeeComment = $bid->employee_comment;
        $this->completeAt = $bid->complete_at;
        $this->works = $works;
    }
}

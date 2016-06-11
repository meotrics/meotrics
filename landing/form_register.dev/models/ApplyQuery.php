<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Apply]].
 *
 * @see Apply
 */
class ApplyQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Apply[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Apply|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

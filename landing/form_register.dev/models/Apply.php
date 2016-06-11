<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "apply".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $dob
 * @property string $file
 * @property string $github
 * @property string $apply
 */
class Apply extends \yii\db\ActiveRecord
{
    public $filedata;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'apply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone'], 'required'],
            [['dob'], 'safe'],
            [['name', 'email', 'phone', 'file', 'github', 'apply'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'dob' => 'DOB',
            'filedata'=>'Upload CV',
            'file' => 'File',
            'github' => 'Github',
            'apply' => 'Apply',
        ];
    }
    public function upload()
    {
        $this->filedata->name = $this->filedata->baseName .'-'. time() . '.' . $this->filedata->extension;
        if ($this->validate()) {
            $this->filedata->saveAs('uploads/' . $this->filedata->baseName . '.' . $this->filedata->extension);
            return true;
        } else {
            return false;
        }
    }
}

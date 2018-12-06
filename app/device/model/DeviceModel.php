<?php

namespace app\device\model;

use think\Model;

class DeviceModel extends Model
{
    
    public function add($data)
    {
        $this->allowField(true)->data($data, true)->save();
        return $this;
    }

    public function edit($data)
    {
        $this->allowField(true)->isUpdate(true)->data($data, true)->save();
        return $this;
    }
  

}


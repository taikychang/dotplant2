<?php

namespace app\widgets;

use app\models\Image;
use Yii;
use yii\base\Widget;

class ImgSearch extends Widget
{
    public $objectId = null;
    public $objectModelId = null;
    public $viewFile = 'img';
    public $limit = null;
    public $offset = 0;

    public function run()
    {
        $cacheKey = "ImgSearch:" . implode("_", [
            $this->objectId,
            $this->objectModelId,
            $this->viewFile,
            $this->limit,
            $this->offset
        ]);
        $result = Yii::$app->cache->get($cacheKey);

        if ($result === false) {
            $images = Image::getForModel($this->objectId, $this->objectModelId);
            if ($this->offset > 0 || !is_null($this->limit)) {
                $images = array_slice($images, $this->offset, $this->limit);
            }
            $result = $this->render(
                $this->viewFile,
                ['images' => $images]
            );
            Yii::$app->cache->set($cacheKey, $result, 86400, new \yii\caching\TagDependency([
                'tags' => 'Images:'.$this->objectId.':'.$this->objectModelId
            ]));
        }


        return $result;
    }
}

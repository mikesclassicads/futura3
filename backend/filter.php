<?php

//    sleep(3);

    class Years {

        public $from, $to;

        function __construct($from, $to) {

            $this->from = $from;
            $this->to = $to;
            $this->defaultFrom = $from;
            $this->defaultTo = $to;
        }
    }

    class Tag {

        public $id, $name, $isBlock, $active;

        function __construct($id, $name, $isBlock = false, $active = false) {

            $this->id = $id;
            $this->name = $name;
            $this->active = $active;
            $this->isBlock = $isBlock;

            return this;
        }

        public function setActive($value) {

            $this->active = $value;
        }

        public function setName($value) {

            $this->name = $value;
        }
    }

    class TagGroup {

        public $id, $name;
        public $tags = array('');
        private $_isBlock;

        function __construct($id, $name, $isBlock = false) {

            $this->id = $id;
            $this->name = $name;
            $this->_isBlock = $isBlock;

            return this;
        }

        public function getTags() {

            return $this->tags;
        }

        public function setTags(array $tags) {

            $this->tags = array();

            for ( $i = 0, $len = count($tags); $i < $len; $i++ ) {
                array_push($this->tags, new Tag($i, $tags[$i], $this->_isBlock));
            }

            return $this;
        }
    }

    $tagsGroups = array();

    function addTagGroup(&$arr, $tagGroup, $tags) {

        array_push($arr, $tagGroup->setTags($tags));
    }

    function getRequest() {

        $requestJSON = json_decode(file_get_contents('php://input'));

        return ( !$requestJSON ) ? null : $requestJSON;
    }

    function updateTagsGroups(&$filter,$request) {

        for ( $i = 0, $len = count($filter); $i < $len; $i++ ) {

            $groupTags = $filter[$i]->getTags();
            $requestTags = $request->tagsGroups[$i]->tags;

            for ( $j = 0, $lenJ = count($groupTags); $j < $lenJ; $j++ ) {

                $groupTag = $groupTags[$j];
                $groupTag->setActive($requestTags[$j]->active);
            }
        }
    }

    $years = new Years(1980,2000);

    addTagGroup($tagsGroups, new TagGroup(1,"Фильтр",true),["Смотреть онлайн","Можно скачать","Можно купить"]);
    addTagGroup($tagsGroups, new TagGroup(2,"Жанр"),array_merge(["Боевик","Аниме","Артхаус","Образовательные программы"],["Боевик","Аниме","Артхаус","Образовательные программы"],["Боевик","Аниме","Артхаус","Образовательные программы"]));
    addTagGroup($tagsGroups, new TagGroup(3,"Страна"),["Боевик","Аниме","Артхаус","Образовательные программы"]);
    addTagGroup($tagsGroups, new TagGroup(4,"Награды"),["Боевик","Аниме","Артхаус","Образовательные программы"]);

    if ( ($request = getRequest()) !== null ) {

        $years->to = $request->years->to;
        $years->from = $request->years->from;
        updateTagsGroups($tagsGroups,$request);

        if ( false ) {

            if ( count($request->tagsGroups) == 2 ) {
                addTagGroup($tagsGroups, new TagGroup(3,"Крутотень"),["Опа","Шмопа"]);
                $tagsGroups[2]->tags[0]->setActive(true);
            }
            else if ( count($request->tagsGroups) == 3 ) {

                array_pop($request->tagsGroups);
            }
        }
    }

    $payload = new StdClass();

    $payload->years = $years;
    $payload->tagsGroups = $tagsGroups;

    if ( !getRequest() ) {

        $payload->tagsGroups[1]->tags[0]->setActive(true);
    }

    echo json_encode($payload);

?>
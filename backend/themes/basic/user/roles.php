<?php

/**
 *
 * @var \yii\web\View $this
 * @var \common\ar\AuthItems $model
 * @var array $menu
 *
 */

\common\assets\FancyTreeAsset::register($this);

$grid = $model::getGrid($model);
?>
<? $grid->run();

$src = $grid->generateJsonCols(0);
$src = \yii\helpers\Json::encode($src);

?>

<? $this->registerJs(<<<JS

var CLIPBOARD = null;

$(function() {
    var src = {$src};
    console.log(src);
$("#GridRoles_table")
  .fancytree({
    checkbox: false,
    checkboxAutoHide: true,
    titlesTabbable: false, // Add all node titles to TAB chain
    quicksearch: false, // Jump to nodes when pressing first character
    strings:{
        loading:'Загрузка',
        loadError:'Ошбка загрузки!',
        moreData:'Еще...',
        noData:'Нет данныех',
    },
    // source: SOURCE,
    source: src,

    extensions: ["table"],
    
    table: {
      indentation: 20,
      nodeColumnIdx: 0,
      checkboxColumnIdx: null,
    },
    renderColumns: function(event, data) {
        var node = data.node,
            tds = jQuery(node.tr).find(">td");
        
        if(typeof(node.data.cols) != "undefined" ){
            jQuery.each(node.data.cols, function(idx, item){
                tds.eq(idx).html(item);
            });
        }
    },
    renderStatusColumns: function(event, data) {
        var node = data.node,
            tds = jQuery(node.tr).find(">td");
        
        jQuery.each(tds, function( index, el ) {
            if(index === 0) return;
            jQuery(el).html('');
        });
    },
    createNode: function(event, data) {
        //console.log(data);
    },
    clickPaging: function(event, data) {
        data.node.replaceWith({url: data.node.data.url}).done(function(){
          // The paging node was replaced with the next bunch of entries.
        });
    },
    lazyLoad:function(event, data){
        var node = data.node;
    
        data.result = jQuery.ajax({
            url: 'role-children',
            method: 'get',
            data: {
                id: node.key
            },
            cache: false
        });
        
      },
    modifyChild: function(event, data) {
      data.tree.info(event.type, data);
    },
  });
});

JS
);
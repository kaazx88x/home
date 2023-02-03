// 百度地图API功能
function G(id) {
    return document.getElementById(id);
}

 var initLat=39.897445;
 var initLong=116.331398;

 if($('#latitude_baidu').val()!='' || $('#latitude_baidu').val()==='0.00000000000000')
 {
     initLat=$('#latitude_baidu').val();
     initLong=$('#longitude_baidu').val();

 }
var baidumap = new BMap.Map("l_map");
var new_point = new BMap.Point(initLong, initLat);
baidumap.centerAndZoom(new_point, 18);                   // 初始化地图,设置城市和地图级别。
baidumap.enableScrollWheelZoom(true);
var baiduMarker = new BMap.Marker(new_point);  // 创建标注
baiduMarker.enableDragging();
baidumap.addOverlay(baiduMarker);              // 将标注添加到地图中
baidumap.panTo(new_point);

var ac = new BMap.Autocomplete(//建立一个自动完成的对象
        {"input": "location_baidu"
            , "location": baidumap
        });

ac.addEventListener("onhighlight", function (e) {
    baidumap.centerAndZoom("北京",12);
    //鼠标放在下拉列表上的事件
    var str = "";
    var _value = e.fromitem.value;
    var value = "";
    if (e.fromitem.index > -1) {
        value = _value.province + _value.city + _value.district + _value.street + _value.business;
    }
    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

    value = "";
    if (e.toitem.index > -1) {
        _value = e.toitem.value;
        value = _value.province + _value.city + _value.district + _value.street + _value.business;
    }
    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    G("searchResultPanel").innerHTML = str;
});

var myValue;
ac.addEventListener("onconfirm", function (e) {    //鼠标点击下拉列表后的事件
    var _value = e.item.value;
    myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
    G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;

    setPlace();
});

function setPlace() {

    baidumap.clearOverlays();    //清除地图上所有覆盖物
    function myFun() {
        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
        baidumap.centerAndZoom(pp, 18);
        baiduMarker.setPosition(pp);
        baidumap.addOverlay(baiduMarker);    //添加标注
        updateCoordinates(pp);
    }
    var local = new BMap.LocalSearch(baidumap, {//智能搜索
        onSearchComplete: myFun
    });
    local.search(myValue);

}

baiduMarker.addEventListener('dragend', function(e) {
	//console.log(e.point.lat,',',e.point.lng);
	updateCoordinates(e.point);
});

function updateCoordinates(pp) {
	$('#latitude').val(pp.lat);
	$('#longtitude').val(pp.lng);
	$('#latitude_baidu').val(pp.lat);
	$('#longitude_baidu').val(pp.lng);
}

////// 百度地图API功能
//function G(id) {
//    return document.getElementById(id);
//}
//
//var icon = new BMap.Icon("/web/marker_red_sprite.png", new BMap.Size(39, 25), {
//    anchor: new BMap.Size(10, 22)
//});
//
//
//var initLat = 39.897445;
//var initLong = 116.331398;
//
//if ($('#latitude_baidu').val() != '' || $('#latitude_baidu').val() === '0.00000000000000')
//{
//    initLat = $('#latitude_baidu').val();
//    initLong = $('#longitude_baidu').val();
//
//}
//
//// initialize marker
//var baiduMarker = new BMap.Marker();
//baiduMarker.enableDragging();
//baiduMarker.setIcon(icon);
//
//var baiduMap = new BMap.Map("l_map");
//// baiduMap.centerAndZoom("巴彦淖尔",7);
//// baiduMap.centerAndZoom("酒泉",5);
//// baiduMap.centerAndZoom("乌鲁木齐",16);
//// baiduMap.centerAndZoom("乌兰察布",9);
//var new_point = new BMap.Point(initLong, initLat);
//baiduMap.centerAndZoom(new_point, 9);
//// baiduMap.addOverlay(new BMap.Marker(point))
//baiduMap.enableScrollWheelZoom(true);
//baiduMap.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT}));
//
//baiduMap.addEventListener('onclick', function (e) {
//    setMarker(e.point);
//    //console.log(e.point.lat,',',e.point.lng);
//});
//
//var ac = new BMap.Autocomplete({//建立一个自动完成的对象
//    "input": "location_baidu"
//    , "location": baiduMap
//});
//
//ac.addEventListener("onhighlight onchange", function (e) {  //鼠标放在下拉列表上的事件
//    baiduMap.centerAndZoom("北京",12);
//    var str = "";
//    var _value = e.fromitem.value;
//    var value = "";
//    if (e.fromitem.index > -1) {
//        value = _value.province + _value.city + _value.district + _value.street + _value.business;
//    }
//    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;
//
//    value = "";
//    if (e.toitem.index > -1) {
//        _value = e.toitem.value;
//        value = _value.province + _value.city + _value.district + _value.street + _value.business;
//    }
//    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
//    G("searchResultPanel").innerHTML = str;
//});
//
//var myValue;
//ac.addEventListener("onconfirm", function (e) {    //鼠标点击下拉列表后的事件
//    var _value = e.item.value;
//    myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
//    G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
//
//    setPlace();
//});
//
//function setMarker(pp) {
//    baiduMap.clearOverlays();
//    baiduMarker.setPosition(pp);
//    updateCoordinates(pp);
//    baiduMap.addOverlay(baiduMarker);
//    baiduMap.centerAndZoom(pp, 18);
//}
//
//
//function setPlace() {
//    function locate() {
//        var pp = local.getResults().getPoi(0).point;
//        setMarker(pp);
//    }
//    var local = new BMap.LocalSearch(baiduMap, {//智能搜索
//        onSearchComplete: locate
//    });
//    local.search(myValue);
//}
//
//baiduMarker.addEventListener('dragend', function (e) {
//    //console.log(e.point.lat,',',e.point.lng);
//    updateCoordinates(e.point);
//});
//
//function updateCoordinates(pp) {
//    $('#latitude_baidu').val(pp.lat);
//    $('#longitude_baidu').val(pp.lng);
//}
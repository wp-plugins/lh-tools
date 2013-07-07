function createMarker(pos, title, thelink) {

var image = 'http://labs.google.com/ridefinder/images/mm_20_red.png';

    var marker = new google.maps.Marker({       
        position: pos, 
      	map: map,  
        title: title,
icon: image
     
    }); 

    google.maps.event.addListener(marker, 'click', function() { 
          window.location = thelink;
    }); 
    return marker;  
}


function bounds_handler(var1) {

for (var i = 0; i < var1.results.bindings.length; i++) {

var myLatlng = new google.maps.LatLng(var1.results.bindings[i].lat.value, var1.results.bindings[i].lng.value);

createMarker(myLatlng, var1.results.bindings[i].title.value, var1.results.bindings[i].s.value)



}


}


function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
fileref.setAttribute("media", "print")
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}



function getElements() {

var x=document.getElementsByTagName("article");

return x[0].getAttribute('itemid');

 }




function json_handler(var1) {


if (var1.results.bindings.length){

expected = var1;


loadjscssfile('http://maps.google.com/maps/api/js?sensor=true&callback=final_test', 'js');

}

}

function final_test(){

var divArray = document.getElementById('map_canvas');


var txt = document.createTextNode("View nearbye articles");
var heading  = document.createElement('h2');
heading.appendChild(txt);
divArray.parentNode.insertBefore(heading,divArray);

var otherspan  = document.createElement('span');

var artimg  = document.createElement('img');
artimg.setAttribute('src', 'http://maps.google.com/mapfiles/ms/icons/red-dot.png');
var txt = document.createTextNode("article location");
otherspan.appendChild(artimg);
otherspan.appendChild(txt);

var artimg  = document.createElement('img');
artimg.setAttribute('src', 'http://labs.google.com/ridefinder/images/mm_20_red.png');
var txt = document.createTextNode("other articles");
otherspan.appendChild(artimg);
otherspan.appendChild(txt);

divArray.parentNode.insertBefore(otherspan,divArray.nextSibling);




divArray.style.width = '100%';
divArray.style.height = '200px';



var myLatlng = new google.maps.LatLng(expected.results.bindings[0].lat.value, expected.results.bindings[0].lng.value);

        var myOptions = {
          center: myLatlng,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };


map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);


start_marker = new google.maps.Marker({
 position: myLatlng,
title: 'Article Location',
map: map
});

google.maps.event.addListener(map, "idle", function() {

mapcanvasdiv = document.getElementById("map_canvas");

endpoint = mapcanvasdiv.getAttribute("data-lh_tools_url");

bar1 = endpoint + '?query=';

var sparqle_query = 'SELECT ?s ?title ?lat ?lng COUNT(?tag) AS ?tags WHERE {<$subject> <http://rdfs.org/sioc/ns#topic> ?tag . ?s <http://rdfs.org/sioc/ns#topic> ?tag . ?s <http://rdfs.org/sioc/ns#topic> ?o . ?s <http://purl.org/dc/elements/1.1/title> ?title . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng . FILTER (?lat >= $sw_latitude) . FILTER (?lat <= $ne_latitude) . FILTER (?lng >= $sw_longitude) . FILTER (?lng <= $ne_longitude) . FILTER(str(?s)!="$subject") } GROUP BY ?s order by desc(?tags) LIMIT 10 OFFSET 0';


var sparqle_query = sparqle_query.replace("$sw_latitude", map.getBounds().getSouthWest().lat());
var sparqle_query = sparqle_query.replace("$ne_latitude", map.getBounds().getNorthEast().lat());
var sparqle_query = sparqle_query.replace("$sw_longitude", map.getBounds().getSouthWest().lng());
var sparqle_query = sparqle_query.replace("$ne_longitude", map.getBounds().getNorthEast().lng());
var sparqle_query = sparqle_query.replace("$subject", subject);
var sparqle_query = sparqle_query.replace("$subject", subject);

var sparqle_query = encodeURIComponent(sparqle_query);

bar2 = '&output=json&callback=bounds_handler';

bar = bar1 + sparqle_query + bar2;

loadjscssfile(bar, 'js');


});


}



subject = getElements();

endpoint = 'http://shawfactor.com/wp-content/plugins/lh-tools/?output=json&callback=json_handler&query=';

sparqle_query = 'SELECT * WHERE { <$subject_var> <http://rdfs.org/sioc/ns#topic> ?o . ?o <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://dbpedia.org/ontology/place> . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat . ?o <http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lng }';

sparqle_query = sparqle_query.replace("$subject_var", subject);

sparqle_query = encodeURIComponent(sparqle_query);

foo = endpoint + sparqle_query;


loadjscssfile(foo, 'js');



function lh_tools_related_articles(){

if (document.getElementById('lh_tools_related_articles_div')){

subject = document.getElementById('lh_tools_related_articles_div').getAttribute('data-uriref');

bar1 = document.getElementById('lh_tools_related_articles_div').getAttribute('data-lh_tools_url') + '?query=';

var sparqle_query = 'SELECT ?s ?thumbnailsize ?title ?abstract COUNT(?topic) AS ?topics WHERE {<$subject> <http://rdfs.org/sioc/ns#topic> ?topic . ?s <http://rdfs.org/sioc/ns#topic> ?topic . ?s <http://purl.org/dc/elements/1.1/title> ?title .  ?s <http://purl.org/dc/elements/1.1/abstract> ?abstract . OPTIONAL { ?s <http://localhero.biz/uri/localhero-namespace/post_thumbnail> ?postthumbnail . ?postthumbnail <http://xmlns.com/foaf/0.1/thumbnail> ?thumbnailsize .  ?thumbnailsize <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://localhero.biz/uri/localhero-namespace/thumbnail>   } . FILTER (?s !=<$subject>) } GROUP BY ?s order by desc(?topics) LIMIT';

if (screen.width > 460){

sparqle_query += ' 3 OFFSET 0';

} else {

sparqle_query += ' 2 OFFSET 0';

}

var sparqle_query = sparqle_query.replace("$subject", subject);

var sparqle_query = sparqle_query.replace("$subject", subject);

var sparqle_query = encodeURIComponent(sparqle_query);

bar2 = '&output=json&callback=lh_tools_related_articles_json_handler';

bar = bar1 + sparqle_query + bar2;


loadjscssfile(bar, 'js');

}

}

function lh_tools_related_articles_json_handler(var1) {


addit = '<h3>Semantically Related Posts</h3><ul class=\"lhtoolsrelatedlist\" style=\"list-style-type:none;margin:0;padding:0;min-height:250px;\">';

for (var i = 0; i < var1.results.bindings.length; i++) {



if (i == 0){

addit += '<li style=\"float:left;margin:0;padding:0;width:150px;\"><a href=\"' + var1.results.bindings[i].s.value + '\">';

} else {

addit += '<li style=\"float:left;margin:0 0 0 2px;padding:0;width:150px;\"><a href=\"' + var1.results.bindings[i].s.value + '\">';

}


if (var1.results.bindings[i].thumbnailsize!== undefined){
addit += '<img src=\"' + var1.results.bindings[i].thumbnailsize.value + '\"/>';
} else {

addit += '<img src=\"' + document.getElementById('lh_tools_related_articles_div').getAttribute('data-lh_tools_url') + 'images/question_mark_thumbnail-150x150.png\"/>';

}
addit += '<br/>' + var1.results.bindings[i].title.value + '</a>' + var1.results.bindings[i].abstract.value + '</li>';




}

addit += '</ul><br/>';

document.getElementById('lh_tools_related_articles_div').innerHTML = addit;

}


lh_tools_related_articles();


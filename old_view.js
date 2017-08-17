// http request wrapper
!function(t,e){function n(t){return t&&e.XDomainRequest&&!/MSIE 1/.test(navigator.userAgent)?new XDomainRequest:e.XMLHttpRequest?new XMLHttpRequest:void 0}function o(t,e,n){t[e]=t[e]||n}var r=["responseType","withCredentials","timeout","onprogress"];t.ajax=function(t,a){function s(t,e){return function(){c||(a(void 0===f.status?t:f.status,0===f.status?"Error":f.response||f.responseText||e,f),c=!0)}}var u=t.headers||{},i=t.body,d=t.method||(i?"POST":"GET"),c=!1,f=n(t.cors);f.open(d,t.url,!0);var l=f.onload=s(200);f.onreadystatechange=function(){4===f.readyState&&l()},f.onerror=s(null,"Error"),f.ontimeout=s(null,"Timeout"),f.onabort=s(null,"Abort"),i&&(o(u,"X-Requested-With","XMLHttpRequest"),e.FormData&&i instanceof e.FormData||o(u,"Content-Type","application/x-www-form-urlencoded"));for(var p,m=0,v=r.length;v>m;m++)p=r[m],void 0!==t[p]&&(f[p]=t[p]);for(var p in u)f.setRequestHeader(p,u[p]);return f.send(i),f},e.nanoajax=t}({},function(){return this}());


// declare some 'CONSTANTS'
// both these values are used:
//  in the SVG layer which is used for highlights
//  in the calculation of which book the mouse is over
//  in the drawing of the 'revealed' staff image overlay
var SHELF_HEIGHT = 733;  // 733 multiplied by shelf number (0 to 31) gives us the bottom value in pix
var X_ADJUST     = 0.9765;  // the left of each book in the model data must be multiplied by this to match the rendered map
var Y_ADJUST     = 122;
var UPDATEURL    = 'update2.php';
var INITIALISEURL= 'initialise.php';
var KEEPALIVEURL = 'http://explore.bl.uk/primo_library/libweb/action/display.do?frbrVersion=2&tabs=moreTab&ct=display&fn=search&doc=BLL01009535560&indx=2&recIds=BLL01009535560&recIdxs=1&elementId=1&renderMode=poppedOut&displayMode=full&frbrVersion=2&dscnt=1&scp.scps=scope:(BLCONTENT)&frbg=&tab=local_tab&dstmp=1472217154611&srt=rank&mode=Basic&vl(488279563UI0)=any&dum=true&tb=t&vl(freeText0)=Please%20Kill%20Me:%20The%20Uncensored%20Oral%20History%20of%20Punk,%20Gillian%20McCain&vid=BLVU1'; // this is a hack
var LOGURL = 'log.php'

// globals
var map;            // map object is container of the layers from leaflet library
var books;          // books is a tilelayer on the map that holds the tiled images of the books
var highlightLayer; // highlightlayer is layer on the map that holds the highlight rollover svg rect

// we store the shelf the mouse is over globally so we can optimise the data access
// by only changing the shelfdata when the mouse moves to another shelf
var shelf;
var shelfData;
var bookFile; // holds the name of the book image file that's currently highlighted
var mouseLoc;
var modal; // refernce to modal win
var bounds = L.latLngBounds(L.latLng(0, 0),L.latLng(-195.3125, 195.3125));
// make instances of the tile layers

books = L.tileLayer('old_tiles/{z}/{x}/{y}.png', {
  minZoom: 2,
  maxZoom: 7,
  continuousWorld: false,
  noWrap: true,
  tileSize: 250,
  bounds: bounds
});

highlightLayer = new L.LayerGroup(); // declare svg roll over highlight layer

// instance the map object
map = L.map('map',{
  attributionControl: false,
  zoomControl: false,
  crs   : L.CRS.Simple,
  layers: [highlightLayer, books],
  maxBounds: L.latLngBounds(L.latLng(1, -1),L.latLng(-195.3125, 195.3125)),  //southWest, northEast
  maxBoundsViscosity: 1.0
})


map.on('load',function(e) {
  initialise();
})

map.setView([0,0], 2);

new L.Control.Zoom({ position: 'bottomright' }).addTo(map);


map.on('mousemove',function mouseMove(e){
  highlightLayer.clearLayers();

  mouseLoc = map.project(e.latlng, map.getMaxZoom());

  // get shelf number from mouseloc.y
  var newShelf = ((mouseLoc.y / SHELF_HEIGHT) | 0);

  if (newShelf < 0) { shelf = 0; }
  if (newShelf < 31){ shelf = 31;}

  if(shelf !== newShelf) {
    shelf = newShelf;
    shelfData = testData[shelf]; // get shelf data
  }

  var len = testData[shelf].length - 1;

  for(var i=0; i < len; i++) {

    var left  = testData[shelf][i].x   * X_ADJUST;
    var right = testData[shelf][i+1].x * X_ADJUST;

    bounds = L.latLngBounds(
      map.unproject([left , (shelf    * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom()),
      map.unproject([right,((shelf+1) * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom())
    );

    // console.log();

    if(bounds.contains(e.latlng)){
      if(!testData[shelf][i].visible){bookFile=null;continue}
      L.rectangle(bounds, {color: "#ffffff", weight: 1}).addTo(highlightLayer);
      bookFile = testData[shelf][i].file;
    }

  }
});

map.on('click',function(e){
  if(!bookFile){return;}
  openModalWindow(bookFile);
})





var testBackGround = function(start,end) {

  for(var j=start; j < end; j++) {

    var len = testData[j].length - 1;

    for(var i=0; i < len; i++) {

      var left  = testData[j][i].x   * X_ADJUST;
      var right = testData[j][i+1].x * X_ADJUST;

      bounds = L.latLngBounds(
        map.unproject([left , (j    * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom()),
        map.unproject([right,((j+1) * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom())
      );

      var imageUrl = 'background_slices/'+j+'_'+i+'.png';
      L.imageOverlay(imageUrl, bounds).addTo(map);
      //L.rectangle(bounds, {color: "#ff7800", weight: 1}).addTo(map);
    }

  }
}

/**
 * getPositionsFromFileName: returns a list of [shelf,position] coordinates
 * that are displaying the image with a filename
 * @param  {string} filename - filename of image
 * @return {array of [shelf,position]} - list of coordinates in the json where the filename is used
 */
var getPositionsFromFileName = function(filename){
  var matches = [];
  var numShelves = testData.length;
  for(var i=0; i < numShelves; i++) {
    var numBooks = testData[i].length
    for(var j=0; j<numBooks; j++) {
      if(testData[i][j].file === filename) {
        matches.push([i,j]);
      }
    }
  }
  return matches;
}

var removeBooksAt = function(positions) {
  var numToRemove = positions.length;
  for(var i=0; i<numToRemove; i++) {

    shelf = positions[i][0];
    position = positions[i][1]
    if(testData[shelf][position].visible == 0) {console.log('done so skipping');continue;} // we already done this one.

    var left  = testData[shelf][position].x   * X_ADJUST;
    var right = testData[shelf][position+1].x * X_ADJUST;

    bounds = L.latLngBounds(
      map.unproject([left ,(shelf    * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom()),
      map.unproject([right,((shelf+1) * SHELF_HEIGHT)+Y_ADJUST],map.getMaxZoom())
    );

    var imageUrl = 'background_slices/'+shelf+'_'+position+'.png';
    L.imageOverlay(imageUrl, bounds).addTo(map);
    testData[shelf][position].visible = 0;
  }
}



function initialise() {
  nanoajax.ajax( {url:UPDATEURL}, function(code,responseText) {
    var books = ( JSON.parse(responseText) );
    for(var i=0; i<books.length; i++){
      var positions = getPositionsFromFileName(books[i]);
      console.log(positions);
      if(positions) {
        removeBooksAt(positions);
      }
    }

    doTest();
  })
}


var deleteBook = function() {
  nanoajax.ajax( {url:UPDATEURL}, function(code,responseText) {
    

    var books = ( JSON.parse(responseText) );
    for(var i=0; i<books.length; i++){
      var positions = getPositionsFromFileName(books[i]);
    }
    console.log(positions);
    if(positions) {
      removeBooksAt(positions);
    }
  })
}
    //removeBooksAt(positions);

// global here for testing purposes

var lastshelf = testData.length;
var currshelf = 0;

function doTest2() {
  setTimeout(function(){
    if (currshelf == lastshelf) { return; }
    testBackGround(currshelf,currshelf+1);
    currshelf++
    doTest2();
  }, 10000);
}

// doTest();


function doTest() {
  setTimeout(deleteBook, 900000);
}

//doTest();



function openModalWindow(dText) {
    var child = document.createElement('div');
        child.className = "inside";
    var text = document.createElement('span');
        text.className = "inside-text";
        text.innerHTML = '<img src="spinner.gif" width="128" height="128" />';

   child.appendChild(text);

    // get promise
    var details = getBookDetails(dText);

    // set promise callback to replace the spinner
    details.then(function(error, details, xhr) {
      if (error) {
          console.log('Error ' + xhr.status);
          text.innerHTML = "Sorry: there was a network problem :-(";
          return;
      }
      text.innerHTML = details;

    });

    //create modal instance and pass in child elements
    //can be whatever, styled however you want
    modal = new Modal(child, true);
    modal.show(); //open the modal window
}

// make a promise that returns a spinner until the 

// promise wrapper
(function(a){function b(){this._callbacks=[];}b.prototype.then=function(a,c){var d;if(this._isdone)d=a.apply(c,this.result);else{d=new b();this._callbacks.push(function(){var b=a.apply(c,arguments);if(b&&typeof b.then==='function')b.then(d.done,d);});}return d;};b.prototype.done=function(){this.result=arguments;this._isdone=true;for(var a=0;a<this._callbacks.length;a++)this._callbacks[a].apply(null,arguments);this._callbacks=[];};function c(a){var c=new b();var d=[];if(!a||!a.length){c.done(d);return c;}var e=0;var f=a.length;function g(a){return function(){e+=1;d[a]=Array.prototype.slice.call(arguments);if(e===f)c.done(d);};}for(var h=0;h<f;h++)a[h].then(g(h));return c;}function d(a,c){var e=new b();if(a.length===0)e.done.apply(e,c);else a[0].apply(null,c).then(function(){a.splice(0,1);d(a,arguments).then(function(){e.done.apply(e,arguments);});});return e;}function e(a){var b="";if(typeof a==="string")b=a;else{var c=encodeURIComponent;var d=[];for(var e in a)if(a.hasOwnProperty(e))d.push(c(e)+'='+c(a[e]));b=d.join('&');}return b;}function f(){var a;if(window.XMLHttpRequest)a=new XMLHttpRequest();else if(window.ActiveXObject)try{a=new ActiveXObject("Msxml2.XMLHTTP");}catch(b){a=new ActiveXObject("Microsoft.XMLHTTP");}return a;}function g(a,c,d,g){var h=new b();var j,k;d=d||{};g=g||{};try{j=f();}catch(l){h.done(i.ENOXHR,"");return h;}k=e(d);if(a==='GET'&&k){c+='?'+k;k=null;}j.open(a,c);var m='application/x-www-form-urlencoded';for(var n in g)if(g.hasOwnProperty(n))if(n.toLowerCase()==='content-type')m=g[n];else j.setRequestHeader(n,g[n]);j.setRequestHeader('Content-type',m);function o(){j.abort();h.done(i.ETIMEOUT,"",j);}var p=i.ajaxTimeout;if(p)var q=setTimeout(o,p);j.onreadystatechange=function(){if(p)clearTimeout(q);if(j.readyState===4){var a=(!j.status||(j.status<200||j.status>=300)&&j.status!==304);h.done(a,j.responseText,j);}};j.send(k);return h;}function h(a){return function(b,c,d){return g(a,b,c,d);};}var i={Promise:b,join:c,chain:d,ajax:g,get:h('GET'),post:h('POST'),put:h('PUT'),del:h('DELETE'),ENOXHR:1,ETIMEOUT:2,ajaxTimeout:0};if(typeof define==='function'&&define.amd)define(function(){return i;});else a.promise=i;})(this);

function getBookDetails(book){
  return promise.get('details.php?filename='+book);
}

function openWindow(address) {
  nanoajax.ajax( {url:LOGURL+"?"+address}, function(code,responseText) {/*discard result this is just for logging*/} );
  console.log(address);
  var windowObjectReference = window.open(address, 'library catalogue', 'directories=no,titlebar=no,toolbar=no,scrollbars=yes,location=no,status=no,menubar=no,width=800,height=640,left=200,top=200');
  modal.unmount();
  return 0;
}

function openWindowE(address) {
  console.log(address);
  var windowObjectReference = window.open(address, 'library catalogue', 'directories=no,titlebar=no,toolbar=no,scrollbars=yes,location=no,status=no,menubar=no,width=600,height=320,left=200,top=200');
  modal.unmount();
  return 0;
}

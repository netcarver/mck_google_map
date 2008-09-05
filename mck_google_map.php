<?php
$plugin['name'] = 'mck_google_map';
$plugin['version'] = '2.0';
$plugin['author'] = 'Casalegno Marco';
$plugin['author_uri'] = 'http://www.kreatore.it';
$plugin['description'] = 'Questo plugin permette di inserire nel proprio sito mappe tratte da Goolge maps';
$plugin['type'] = '1';

@include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
function mck_google_js ($atts) {
  extract(lAtts(array(
    'apikey' => ''
  ),$atts));
  if(!$apikey){
    return "<!-- Your API key is missing. If you don't have one go to http://www.google.com/apis/maps/signup.html -->";
  }
  else{
   return '<script src="http://maps.google.com/maps?file=api&amp;v=1&amp;key='.$apikey.'" type="text/javascript"></script>'.n;
  }
}

function mck_google_map ($atts) {
    extract(lAtts(array(
    'center' => '45.689274,9.105949',
    'zoom' => '12',
    'idmap' => 'map',
    'width' => '500',
    'height' => '300',
    'mapctrl' => '',
    'typectrl' => '',
    'mark' => '',
    'markpoint' => 'map.getCenter()',
    'poly' =>''
  ),$atts));


//valuto ed inserisco il controllo mappa
if($mapctrl!=""){$ctrlscript="map.addControl(new GSmallMapControl());\n";}

//valuto ed inserisco il controllo tipologia mappa
if($typectrl!=""){$typescript="map.addControl(new GMapTypeControl());\n";}

//valuto ed inserisco il marker
if($mark!=""){
$scriptmarker="\n
var marker = new GMarker($markpoint);\n
GEvent.addListener(marker, \"click\", function() {\n
  marker.openInfoWindowHtml(\"<span style='color:#000'>$mark</span>\");\n
  });\n
map.addOverlay(marker);
";
}
else{$scriptmarker="";}


//valuto ed inserisco le polyline
if($poly!=""){
list($pline,$plevel) = split(",", $poly, 2);
$polyscript="
   var encodedPolyline = new GPolyline.fromEncoded({
    color: \"#FF0000\",
    weight: 10,
    points: \"$pline\",
    levels: \"$plevel\",
    zoomFactor: 32,
    numLevels: 4
   });
   map.addOverlay(encodedPolyline);
";
}



//preparo il codice da scrivere nella pagina
$script="\n<div id=\"$idmap\" style=\"width:".$width."px; height:".$height."px;\"></div>\n";
$script.="<script type=\"text/javascript\">\n
    			//<![CDATA[\n
         window.onload = show$idmap;
            function show$idmap() {\n\n
    	      if (GBrowserIsCompatible()) {\n
      		var map = new GMap(document.getElementById(\"$idmap\"));\n
$ctrlscript
$typescript
                var center = new GLatLng($center);
                map.setCenter(center, $zoom);\n
              }\n
$polyscript
$scriptmarker
       }\n
    //]]>\n
    </script>\n";

return $script;
}

//////////////////////////////////////////////////////////////////////////////
//   Sezione di amministrazione
//////////////////////////////////////////////////////////////////////////////

if (@txpinterface == 'admin') {
register_callback('mck_google_admin', 'article');
}

function mck_google_admin(){
$js = <<<EOF

<script language="javascript" type="text/javascript">
<!--
var side = document.getElementById('textile_help');
var ps = side.getElementsByTagName('p');
var p = document.createElement('p');
p.className="small";
p.innerHTML = '<a target="_blank" href="http://www.kreatore.it/textpattern/polylines.html" onclick="popWin(this.href, 500, 500); return false;">&#60;txp:google_map /></a>';
//side.appendChild(p);
side.insertBefore(p,ps[2]);
// -->
</script>

EOF;

echo $js;

}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
<h3>Descrizione</h3>
<p>Questo plugin permette  l'inserimento di mappe generate da Google Maps, direttamente negli articoli o nelle pagine, senza necessariamente conoscere il metodo richiesto da google.
Inoltre permette di visualizzare dei marker(puntatori) con una finestra di informazioni sul  punto selezionato.<br />
Permette anche l'inserimento di linee guida, quelle che google chiama polylines, e di cui mette anche a disposizione <a href="http://www.google.com/apis/maps/documentation/polylineencoding.html">un&#8217;esempio</a></p>
<p>L&#8217;inserimento da parte dell&#8217;utente dei singoli valori cartesiani, non è però cosa facile. Mi sono allora mosso, per realizzare, nella pagina di aiuto, una copia dell&#8217;&#8220;interactive utility&#8221;:http://www.google.com/apis/maps/documentation/polylineutility.html messo a disposizione da google per la gestione delle polylines.</p>
<p>Tramite questa pagina è ora molto semplice realizzare mappe centrate sull&#8217;italia e allo stesso modo, percorsi. C&#8216;è da ricordare però che come suggerisce google nella sua <a href="http://www.google.com/apis/maps/documentation/">documentazione sulle API</a> <em>...Long and complicated lines require a fair amount of memory, and often may take longer to draw&#8230;</em> La realizzazione di linee lunge e complicate richiede una considerevole quantità di memoria e spesso impiega tempo per essere disegnate.</p>
<h3>Metodo d'uso</h3>
<p>Recuperare la propria <span class="caps">API</span> Key presso la <a href="http://www.google.com/apis/maps/signup.html">pagina di configurazione</a> proposta da Google (bisogna preventivamente creare un account).</p>
<p>Quindi nel <em style="text-align:left;">head></em> della pagina, inserire il comando<br />
<code>&#60;txp:mck_google_js apikey=&#34;xxxx&#34; /&#62;</code><br />
dove il valore di <em>apikey</em> sarà l&#8217;APIkey generata da Google.</p>
<p>Per inserire invece una mappa, sia in un articolo, sia in una pagina, usare il comando <code>&#60;txp:mck_google_map /&#62;</code> che visualizza di default la cartina della provincia di Como. (è possibile modificarne il puntamento iniziale modificando i valori di default tramite l&#8217;amministrazione dei plugin)</p>
<h3>Estensioni</h3>

<p>Le estensioni accettate per <strong style="text-align:left;">txp:mck_google_js /></strong> sono:
 * <strong>apikey</strong>: <em>(Obbligatorio)</em> Inseire l&#8217;<span class="caps">API</span> key generata da google.</p>

<p>Le estensioni accettate per <strong style="text-align:left;">txp:mck_google_map /></strong> sono:</p>
	<ul>
		<li><strong>idmap</strong>: Inserire l&#8217;ID da assegnare al &#60;div> che visualizzerà la mappa. Utile se si vogliono visualizzare più mappe nella stessa pagina. <em>Default=&#8216;map&#8217;</em></li>
		<li><strong>center</strong>: Inserire i valori cartesiani (latitudine e longitudine) da utilizzare come punto centrale della mappa. <em>Default=&#8216;45.689274,9.105949&#8217; (coordinate di Como)</em></li>

		<li><strong>zoom</strong>: Inserire il valore di zoom con il quale visualizzare la mappa. I valori vanno da un minimo di 0(intero globo) a 17(visualizzate anche i tombini!!!). <em>Default=&#8216;12&#8217;</em></li>
		<li><strong>width</strong>: Inserire il valore in pixel per la larghezza della mappa. <em>Default=&#8216;500&#8217;</em></li>
		<li><strong>height</strong>: Inserire il valore in pixel per l&#8217;altezza della mappa. <em>Default=&#8216;300&#8217;</em></li>

		<li><strong>mapctrl</strong>: Binario, abilita la visualizzazione dei pulsanti di controllo della mappa: zoom e movimenti (la mappa è comunque draggabile). <em>Default=&#8216;0&#8217;</em></li>
		<li><strong>typectrl</strong>: Binario, abilita la visualizzazione dei pulsanti per la scelta del tipo di mappa. I pulsanti visualizzati permetteranno di passare dalla visualizzazione classica &#8216;mappa&#8217; alla visualizzazione satellitare, a quella ibrida. <em>Default=&#8216;0&#8217;</em></li>
		<li><strong>mark</strong>: Inserire il testo da visualizzare come finestra informazioni per un singolo marker. Lasciandolo vuoto non visualizzerà nessun marker.</li>

		<li><strong>markpoint</strong>: Inserire le coordinate ove verrà posizionato il marker (bisogna inserire il testo all&#8217;estensione <em>mark</em>). Se lasciato vuoto visualizzerà il marker al centro della mappa, sui valori passati con &#8216;center&#8217;. </li>
	</ul>
# --- END PLUGIN HELP ---
-->
<?php
}
?>

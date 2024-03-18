<?php 
		use Genetsis\Identity;
		use Genetsis\UserApi;
	 add_action( 'wp_enqueue_scripts', 'wog_enqueue_styles' );
	 function wog_enqueue_styles() {
 		  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
		   } 
		   

	function show_post($path) {
		$post = get_page_by_path($path);
		$content = apply_filters('the_content', $post->post_content);
		echo $content;
	}


	/* WIDGET ZONE LATERAL BANNER PLAN */
	function login_zone_widget() {
		register_sidebar(
			array(
				'id'            => 'loginwidget',
				'name'          => __( 'Login Header Widget' ),
				'description'   => __( 'Zona Header Login para mostrar login de Druid' ),
				'before_widget' => '<div class="login">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>'
			)
		);
	}
	add_action( 'widgets_init', 'login_zone_widget' );

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}

//if ($_SERVER['SERVER_NAME']=='demo-druid.testgenetsis.com'):
	function add_gtm_footer() { 
		if (Identity::isConnected()):
			$info = UserApi::getUserLogged();
			$entry_=$info->user->{'entry-point'};
			$oid_=$info->user->oid;
			$event_='druidEvent';
            $page_id = get_queried_object_id();

            switch ($page_id) {
				case '2':
					$druidCategory_='Page';
					$druidAction_='Viewed';
					$druidLabel_='Home';
				 break;
				 case '45':
					$druidCategory_='Page';
					$druidAction_='Viewed';
					$druidLabel_='PromoFan';
				 break;
				 case '85':
					$druidCategory_='Page';
					$druidAction_='Viewed';
					$druidLabel_='SuperTrajePro';
				 break;
				 case '87':
					$druidCategory_='Page';
					$druidAction_='Viewed';
					$druidLabel_='MoonRocket';
				 break;
				 case '64':
					$druidCategory_='Promotion';
					$druidAction_='Participated';
					$druidLabel_='PromoFan';
				 break;
				default:
					$druidCategory_='Page';
					$druidAction_='Viewed';
					$druidLabel_='Home';
					break;
			}?>	

			<script type="text/javascript">
				dataLayer = [{
			        	'objectId': '<?php echo $oid_;?>',
			 			'entryPoint':'<?php echo $entry_;?>'
			    }];
			    dataLayer.push({'event': '<?php echo $event_;?>',
                        'druidCategory': '<?php echo $druidCategory_;?>',
                        'druidAction': '<?php echo $druidAction_;?>',
                        'druidLabel': '<?php echo $druidLabel_;?>'});
			</script>

		<?php endif;?>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-KHGX3QJ');</script>
		<!-- End Google Tag Manager -->
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KHGX3QJ"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php 
	}
	add_action('wp_footer','add_gtm_footer',2);
//endif;

function wpb_my_discount_shortcode() { 
	$protocol = isset($_SERVER['HTTPS'])===true ? 'https://' : 'http://';
	if (Identity::isConnected()):
			$info = UserApi::getUserLogged();
			$oid_=$info->user->oid;
		//$code_ = 'https://www.dna.demo.dru-id.com/s/wog/'.$oid_.'?redirect_uri='.$protocol.$_SERVER['SERVER_NAME'].'/que-es-wogtechpro/'; 
		$code_ = 'https://www.dna.demo.dru-id.com/s/wog/'.$oid_.'?redirect_uri='.urlencode('https://www.rewards.demo.dru-id.com/marketing/a3deee75fdd450e33461d9e66a941a6f9111a47181591c390fe39e5cbeb3980b?provider=druid&uid='.$oid_); 
	else:
		$code_ = $protocol.$_SERVER['SERVER_NAME'].'/que-es-wogtechpro/'; 
	endiF;
	return $code_;
} 
// register shortcode
add_shortcode('my_discount', 'wpb_my_discount_shortcode');

// ------------ Rewards ----------------
// Hook para usuarios no logueados
add_action('wp_ajax_nopriv_exchange_click', 'exchange_click');

// Hook para usuarios logueados
add_action('wp_ajax_exchange_click', 'exchange_click');

// Función que procesa la llamada AJAX

function exchange_click(){

	// Autenticación
	$url = "https://api.rewards.demo.dru-id.com";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url."/auth");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$data = array(
	    'username' => '932421355115277',
	    'password' => 'fn8F0dgetNPwp7f7HhA7MOjIVWNI1s'
	);
	$body = json_encode($data);
	$header = array('Content-Type:application/json');
	curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	$output = curl_exec($curl);
	curl_close($curl);

	function arr_header($output){
		$headers = [];
		$output = rtrim($output);
		$data = explode("\n",$output);
		$headers['status'] = $data[0];
		array_shift($data);
		foreach($data as $part){
		    //some headers will contain ":" character (Location for example), and the part after ":" will be lost, Thanks to @Emanuele
		    $middle = explode(":",$part,2);
		    //Supress warning message if $middle[1] does not exist, Thanks to @crayons
		    if ( !isset($middle[1]) ) { $middle[1] = null; }
		    $headers[trim($middle[0])] = trim($middle[1]);
		}
		return $headers;
	}

	$headers= arr_header($output);
	// Autenticación - obtenemos Bearer de la respuesta del header
	$auth_= $headers['authorization'];
	// QR - Obtenemos objectId del QR
	$qr_=$_POST['qr'];
	$curl = curl_init($url);
	$params = array('key' => $qr_);
	$url_ = $url . '/qrs/search/byKey?' . http_build_query($params);
	curl_setopt($curl, CURLOPT_URL, $url_);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$header = array("Authorization: ".$auth_);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($curl, CURLOPT_HEADER, true);
	$output = curl_exec($curl);
	curl_close($curl);

	$output_= json_decode($output);
	if ($output_->objectId):
		//echo "codigo correcto";
		// Si Obtenemos objectId del QR, redimimos.
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url."/marketing/qrs/".$output_->objectId);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$data = '{
		    "context" : {
		        "course" : '.$_POST['course'].'
		    }
		}';
		//$body = json_encode($data);
		$header = array('Content-Type:application/json');
		$header = array("Content-Type:application/json","Authorization: ".$auth_);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		$output = curl_exec($curl);
		curl_close($curl);
		$headers= arr_header($output);
		if (trim($headers["status"]) =="HTTP/2 200")
			wp_send_json( array('state' => 1, 'wpduf') );
		else
			wp_send_json( array('state' => 2, 'wpduf') );
	else:
		wp_send_json( array('state' => -1, 'wpduf') );
	endif;
}
//--------------------------------------
?>

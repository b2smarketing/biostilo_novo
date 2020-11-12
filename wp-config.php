<?php
define('WP_CACHE', false); // Added by WP Rocket

/** Enable W3 Total Cache */
 // Added by WP Hummingbird


/** Enable W3 Total Cache */


/** Enable W3 Total Cache */




/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */
define('WP_HOME','https://biostilo.com:444');
define('WP_SITEURL','https://biostilo.com:444');
define('FS_METHOD','direct');
define( 'WP_MEMORY_LIMIT', '1024M' );
// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', "biostilo_novo" );


/** Usuário do banco de dados MySQL */
define( 'DB_USER', "root" );


/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', "efc;2505xx" );


/** Nome do host do MySQL */
define( 'DB_HOST', "localhost" );


/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );


/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/** pt_BR! */
define ('WPLANG', 'pt_BR');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'q*>&L@[$0_.F8W++4Q|>(p;s%^Lr/4u<7aSS|-YB~,h+!&m^OGS2C18W4H>+b[wt');
define('SECURE_AUTH_KEY',  'LA7|6V-4rnN$=Rmc^M&)Y)#K+u!A,[4U[0u+erD]q+|v|~E6@9E@1P)8L2[%/ZH*');
define('LOGGED_IN_KEY',    'DZOjPT]7q4/{-fNf&B&wI-uN-5Hek~|V2LD,5,Y*<CjMGIHAh A>^;1j~sdYUn@C');
define('NONCE_KEY',        '*|nJ<+|q[|irM}u|0G7j7S9:AM#Bkl/+S)M~SI/Y:;vOH~rS.|do&|lO|qQL9OFr');
define('AUTH_SALT',        '8f_)Q$aIuK%;Z!~No+i~-&fx_,[aBt3Tj Ho_z+p-J(V{?)z;2b`-_g4&pk&Vd@x');
define('SECURE_AUTH_SALT', 'D2tv9h%(c:EJ|w+WokqomNz+7nk1Rjb}9a<?+dX7|{}6}6PCk25f_*>:Pf}l{&8&');
define('LOGGED_IN_SALT',   '7~m#X6sT@ISL)w+4.?#xJ+Or3koTED{14M_!o2qgr3cfn7|?NL;PQa*Q:+4C6E!8');
define('NONCE_SALT',       'p8)fOp7fE8z^c64LNDKXb^x.tyv&x2N,w_C8C+|nSjc_T =JMB@5p(-g`AjcR-,i');


/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';


/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

define( 'WPMS_ON', false );
//define( 'WPMS_SMTP_PASS', 'Ecommerce@2020@' );

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

define( 'DISALLOW_FILE_EDIT', true );

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');

<?php
session_start(); // Inicia sessão
$num = explode('-', $_SESSION['num_dh']); // Recolhe em formato de array os números selecionados

$dh_file = file_get_contents('dh.json'); // Ler JSON
$dh = json_decode($dh_file, true); // Decodifica JSON

// Valores usados
$Xa = $dh['users'][$_SESSION['username']];
$Xb = (int)$_GET['user2'];
$q = (int)$num[0];
$a = (int)$num[1];

// Primeiros cálculos
$Ya = intval(fmod(pow($a, $Xa), $q));
$Yb = intval(fmod(pow($a, $Xb), $q));

echo '<b>q</b>:' . $q;
echo '<br/><b>a</b>:' . $a . '<br/>';
echo '<b>Xa</b>:' . $Xa . '<br/>';
echo '<b>Xb</b>:' . $Xb . '<br/>';
echo '<p>(Ya) a<sup>Xa</sup> mod q = ' . $a . '<sup>' . $Xa . '</sup>%' . $q . ' = ' . $Ya . '</p>';
echo '<p>(Yb) a<sup>Xb</sup> mod q = ' . $a . '<sup>' . $Xb . '</sup>%' . $q . ' = ' . $Yb . '</p>';
echo '<br/>';

// Segundos cálculos
$psk_alice = intval(fmod(pow($Yb, $Xa), $q));
$psk_bob = intval(fmod(pow($Ya, $Xb), $q));
echo '<p>(PSK) Yb<sup>Xa</sup> mod q = ' . $Yb . '<sup>' . $Xa . '</sup>%' . $q . ' = ' . $psk_alice . '</p>';
echo '<p>(PSK) Ya<sup>Xb</sup> mod q = ' . $Ya . '<sup>' . $Xb . '</sup>%' . $q . ' = ' . $psk_bob . '</p>';

// Salva os dados usados
$dh['config']['q'] = $q;
$dh['config']['a'] = $a;
$dh['config']['Xa'] = $Xa;
$dh['config']['Xb'] = $Xb;
$dh['config']['last_update'] = $_SESSION['username'] . '-' . date("d/m/Y H:i:s");
$json = fopen('dh.json', 'w+');
fwrite($json, json_encode($dh));
fclose($json);

if ($psk_alice == $psk_bob) {
    ?>
    <form method="get" action="dh_finish.php">
        <input type="hidden" name="psk" value="<?php echo $psk_alice; ?>"/>
        <input type="submit" value="CONTINUAR"/>
    </form>
    <?php
} else {
    ?>
    <h1>Error.</h1>
    <h3>SUGESTAO: Tente com outros valores</h3>
    <p>Normalmente isso ocorre devido ao uso de valores muito grande para os cálculos.
        Recomendamos o uso de valores menores.</p>

    <form method="get" action="../../../chat.php">
        <input type="submit" value="OK"/>
    </form>
    <?php
}

?>


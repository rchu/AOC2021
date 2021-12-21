<?php

function my_hex_bin($hex)
{
    $bin = '';
    for ($i=0; $i < strlen($hex); $i++) { 
        $bin .= sprintf('%04b', hexdec($hex[$i]));
    }
    return $bin;
}
/** Returns first $length characters of $str. Read to the next 4bit group if $length is null; */
function str_read(&$str, $length = null) {
    static $read = 0;
    if (is_null($length)) $length = 4 - ($read % 4);

    $result = substr($str,0,$length);
    $str = substr($str,$length);
    $read += $length;
    return $result;
}

function add_subpacket(&$packet, $subpacket) 
{
    if (!isset($packet['subpackets']))
        $packet['subpackets'] = [$subpacket];
    else
        $packet['subpackets'][] = $subpacket;
    $packet['version_sum'] += $subpacket['version_sum'];
}
function read_packet(&$input) {

    $packet = [
        'version' => bindec(str_read($input,3)),
        'type' => bindec(str_read($input,3)),
    ];
    $packet['version_sum'] = $packet['version'];

    # version 4 is a literal value
    if ($packet['type'] == 4): 
        $packet['value'] = '';
        while (true) {
            $num = str_read($input, 1);
            $packet['value'] .= str_read($input,4);
            if ($num[0] == '0') {
                // printf("Reading remainder of '%s': '%s'\n", $input,  str_read($input));
                $packet['value'] = bindec($packet['value']);
                break;
            }
        }  
    # other versions are operators
    else:

        # type 1: length
        if (($packet['length_type_id'] = str_read($input,1)) == 0) {
            $subpackets = str_read(
                $input,
                $packet['subpackets_bits'] = bindec(str_read($input, 15)),
            );
            while ($subpackets)
                add_subpacket($packet, read_packet($subpackets));

        }
        #type 0: count
        else {
            $packet['subpackets_count'] = bindec(str_read($input, 11));
            for ($i=0; $i < $packet['subpackets_count']; $i++) { 
                add_subpacket($packet, read_packet($input));
            }
        }
        
        switch ($packet['type']) {
            case 0: 
                $packet['value'] = array_sum(array_map(function($p) { return $p['value'];}, $packet['subpackets']));
            break;
            case 1: 
                $packet['value'] = array_product(array_map(function($p) { return $p['value'];}, $packet['subpackets']));
            break;
            case 2: 
                $packet['value'] = min(array_map(function($p) { return $p['value'];}, $packet['subpackets']));
            break;
            case 3: 
                $packet['value'] = max(array_map(function($p) { return $p['value'];}, $packet['subpackets']));
            break;
            case 5: 
                $packet['value'] = $packet['subpackets'][0]['value'] > $packet['subpackets'][1]['value'];
            break;
            case 6: 
                $packet['value'] = $packet['subpackets'][0]['value'] < $packet['subpackets'][1]['value'];
            break;
            case 7: 
                $packet['value'] = $packet['subpackets'][0]['value'] == $packet['subpackets'][1]['value'];
            break;
        }
    endif;

    return $packet;
}

function main($input, $text = '') {
    $input =  my_hex_bin($input,16,2);
    $input_len = strlen($input)/4;
    $input = sprintf("%-0{$input_len}s",$input);
    $packet = read_packet($input);
    printf("value=%d, version_sum = %d. $text\n", $packet['value'],$packet['version_sum']);
}

main('8A004A801A8002F478', 'version_sum=16');
main('620080001611562C8802118E34', 'version_sum=12');
main('C0015000016115A2E0802F182340','version_sum=23');
main('A0016C880162017C3686B18A3D4780','version_sum=31');

main('C200B40A82', 'value=3');
main('04005AC33890', 'value=54');
main('880086C3E88112', 'value=7');
main('CE00C43D881120', 'value=9');
main('D8005AC2A8F0', 'value=1');
main('F600BC2D8F', 'value=0');
main('9C005AC2F8F0', 'value=0');
main('9C0141080250320F1802104A08', 'value=1');

main('6053231004C12DC26D00526BEE728D2C013AC7795ACA756F93B524D8000AAC8FF80B3A7A4016F6802D35C7C94C8AC97AD81D30024C00D1003C80AD050029C00E20240580853401E98C00D50038400D401518C00C7003880376300290023000060D800D09B9D03E7F546930052C016000422234208CC000854778CF0EA7C9C802ACE005FE4EBE1B99EA4C8A2A804D26730E25AA8B23CBDE7C855808057C9C87718DFEED9A008880391520BC280004260C44C8E460086802600087C548430A4401B8C91AE3749CF9CEFF0A8C0041498F180532A9728813A012261367931FF43E9040191F002A539D7A9CEBFCF7B3DE36CA56BC506005EE6393A0ACAA990030B3E29348734BC200D980390960BC723007614C618DC600D4268AD168C0268ED2CB72E09341040181D802B285937A739ACCEFFE9F4B6D30802DC94803D80292B5389DFEB2A440081CE0FCE951005AD800D04BF26B32FC9AFCF8D280592D65B9CE67DCEF20C530E13B7F67F8FB140D200E6673BA45C0086262FBB084F5BF381918017221E402474EF86280333100622FC37844200DC6A8950650005C8273133A300465A7AEC08B00103925392575007E63310592EA747830052801C99C9CB215397F3ACF97CFE41C802DBD004244C67B189E3BC4584E2013C1F91B0BCD60AA1690060360094F6A70B7FC7D34A52CBAE011CB6A17509F8DF61F3B4ED46A683E6BD258100667EA4B1A6211006AD367D600ACBD61FD10CBD61FD129003D9600B4608C931D54700AA6E2932D3CBB45399A49E66E641274AE4040039B8BD2C933137F95A4A76CFBAE122704026E700662200D4358530D4401F8AD0722DCEC3124E92B639CC5AF413300700010D8F30FE1B80021506A33C3F1007A314348DC0002EC4D9CF36280213938F648925BDE134803CB9BD6BF3BFD83C0149E859EA6614A8C');
?>
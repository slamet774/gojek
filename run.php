<?php
date_default_timezone_set('Asia/Jakarta');
include "function.php";
$voucher1 = "PESANGOFOOD0607"; // Ganti disini
$voucher2 = "COBAGOFOOD0607"; // Ganti disini
$voucher3 = "PAKEGOFOOD0607"; // Ganti disini
$voucher4 = "EATLAH"; // Ganti disini
$sec = "20";

ulang:
echo color("blue", "Auto Create Go-Jek Account + Auto Redeem Go-Food Voucher\n\n");
    // $nama = nama();
    $users = randomuser();
    $nama = $users['firstname']." ".$users['lastname'];
    $mail = $users['email'];
    $pecah = explode("@",$mail);
    $email = $pecah[0];
    echo color("blue", "[?] Nomor : ");
    // $no = trim(fgets(STDIN));
    $nohp = trim(fgets(STDIN));
    $nohp = str_replace("62", "62", $nohp);
    $nohp = str_replace("(", "", $nohp);
    $nohp = str_replace(")", "", $nohp);
    $nohp = str_replace("-", "", $nohp);
    $nohp = str_replace(" ", "", $nohp);

    if (!preg_match('/[^+0-9]/', trim($nohp))) {
        if (substr(trim($nohp) , 0, 3) == '62'){
            $hp = trim($nohp);
        } else if (substr(trim($nohp) , 0, 1) == '0') {
            $hp = '62' . substr(trim($nohp) , 1);
        } elseif (substr(trim($nohp) , 0, 2) == '62') {
            $hp = '6' . substr(trim($nohp) , 1);
        } else {
            $hp = '1' . substr(trim($nohp) , 0, 13);
        }
    }
    $data = '{"email":"' . $email . '@gmail.com","name":"' . $nama . '","phone":"+' . $hp . '","signed_up_country":"ID"}';
    $register = request("/v5/customers", null, $data);
    if (strpos($register, '"otp_token"')){
        $otptoken = getStr('"otp_token":"', '"', $register);
        echo color("green", "[+] Kode verifikasi sudah di kirim") . "\n";
        otp:
            echo color("blue", "[?] Otp: ");
            $otp = trim(fgets(STDIN));
            $data1 = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $otptoken . '"},"client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e"}';
            $verif = request("/v5/customers/phone/verify", null, $data1);
            if (strpos($verif, '"access_token"')){
                echo color("green", "[+] Berhasil mendaftar\n");
                $token = getStr('"access_token":"', '"', $verif);
                $uuid = getStr('"resource_owner_id":', ',', $verif);
                echo color("green", "[+] Your access token : " . $token . "\n\n");
                save("token.txt", $token);
                echo color("blue", "\n===========(REDEEM VOUCHER)===========");
                echo "\n" . color("white", "[!] Claim $voucher1");
                echo "\n" . color("yellow", "[!] Please wait $sec seconds");
                sleep($sec);
                $code1 = request('/go-promotions/v1/promotions/enrollments', $token, '{"promo_code":"' . $voucher1 . '"}');
                $message = fetch_value($code1, '"message":"', '"');
                if (strpos($code1, 'Promo kamu sudah bisa dipakai')) {
                    echo "\n" . color("green", "[+] Message: " . $message);
                    goto voc2;
                } else {
                    echo "\n" . color("red", "[-] Message: " . $message);
                    // goto pin;
                    voc2:
                    echo "\n" . color("white", "[!] Claim $voucher2");
                    echo "\n" . color("yellow", "[!] Please wait $sec seconds");
                    sleep($sec);
                    $code1 = request('/go-promotions/v1/promotions/enrollments', $token, '{"promo_code":"' . $voucher2 .'"}');
                    $message = fetch_value($code1, '"message":"', '"');
                    if (strpos($code1, 'Promo kamu sudah bisa dipakai.')) {
                        echo "\n" . color("green", "[+] Message: " . $message);
                        goto voc3;
                    } else {
                        echo "\n" . color("red", "[+] Message: " . $message);

                        voc3:
                        echo "\n" . color("white", "[!] Claim $voucher3");
                        echo "\n" . color("yellow", "[!] Please wait $sec seconds");
                        sleep($sec);
                        $code1 = request('/go-promotions/v1/promotions/enrollments', $token, '{"promo_code":"' . $voucher3 .'"}');
                        $message = fetch_value($code1, '"message":"', '"');
                        if (strpos($code1, 'Promo kamu sudah bisa dipakai.')) {
                            echo "\n" . color("green", "[+] Message: " . $message);
                            goto voc4;
                        } else {
                            echo "\n" . color("red", "[+] Message: " . $message);

                            voc4:
                            echo "\n" . color("white", "[!] Claim $voucher4");
                            echo "\n" . color("yellow", "[!] Please wait $sec seconds");
                            sleep($sec);
                            $code1 = request('/go-promotions/v1/promotions/enrollments', $token, '{"promo_code":"' . $voucher4 .'"}');
                            $message = fetch_value($code1, '"message":"', '"');
                            if (strpos($code1, 'Promo kamu sudah bisa dipakai.')) {
                                echo "\n" . color("green", "[+] Message: " . $message);
                            } else {
                                echo "\n" . color("red", "[+] Message: " . $message);

                                $cekvoucher = request('/gopoints/v3/wallet/vouchers?limit=13&page=1', $token);
                                $js = json_decode($cekvoucher, true);
                                $data = $js['data'];
                                for ($i=0; $i < count($data); $i++) {
                                  $vocnya = $data[$i]['title'];
                                  echo "\n" . color("blue", "$vocnya ");
                                }
                                goto ulang;

                                // pin:
                                // ## SET PIN
                                // echo "\n" . color("blue", "[?] SET PIN SEKALIAN? y/n ");
                                // $pilih1 = trim(fgets(STDIN));
                                // if ($pilih1 == "y" || $pilih1 == "Y") {
                                //   echo color("blue", "========( PIN MU = 112233 )========") . "\n";
                                //   $data2 = '{"pin":"112233"}';
                                //   $getotpsetpin = request("/wallet/pin", $token, $data2, null, null, $uuid);
                                //   echo color("blue", "[?] Otp Pin: ");
                                //   $otpsetpin = trim(fgets(STDIN));
                                //   $verifotpsetpin = request("/wallet/pin", $token, $data2, null, $otpsetpin, $uuid);
                                //   echo $verifotpsetpin;
                                // } else if ($pilih1 == "n" || $pilih1 == "N") {
                                //   die();
                                // } else {
                                //   echo color("red", "[-] GAGAL!!!\n");
                                // }
                            }
                        }
                    }
                }
            } else {
              echo color("red", "[-] Otp yang anda input salah");
              echo "\n==================================\n\n";
              echo color("yellow", "[!] Silahkan input kembali\n");
              goto otp;
            }
    } else {
      echo color("red", "[-] Nomor sudah teregistrasi");
      echo "\n==================================\n\n";
      echo color("yellow", "[!] Silahkan registrasi kembali\n");
      goto ulang;
    }

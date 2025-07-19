#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <UniversalTelegramBot.h> // Main library for Telegram Bot interaction
#include <ArduinoJson.h>          // For parsing and creating JSON (used Firebase)
#include <time.h>                 // For NTP time synchronization (important for HTTPS/Telegram)
#include <ESP32Servo.h>           // For servo motor control

// Firebase-ESP-Client libraries
#include <Firebase_ESP_Client.h>
#include <addons/TokenHelper.h>

// --- Debugging Control ---
// Uncomment the line below to enable Serial.print debug messages.
#define DEBUG

#ifdef DEBUG
#define DPRINT(x) Serial.print(x)
#define DPRINTLN(x) Serial.println(x)
#define DPRINTF(...) Serial.printf(__VA_ARGS__)
#else
#define DPRINT(x)
#define DPRINTLN(x)
#define DPRINTF(...)
#endif

// --- Firebase Configuration ---
// MAKE SURE TO REPLACE with your actual Firebase project details!
#define FIREBASE_HOST "celengan-7c473-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "AIzaSyC4gqxOGLjcnZlWcHnp6BCa53MWY9kf5kU" // Your API Key
#define DATABASE_URL "https://celengan-7c473-default-rtdb.firebaseio.com/"

// Student ID for Firebase operations - This should be dynamic or configurable in a real application
String studentId = "-OQf-tSgpuNrQXvXTNlc";

// --- ESP32 Pin Definitions ---
#define SDA_PIN 21
#define SCL_PIN 22
#define SERVO_PIN 12
#define S2 2    // S2 pin of color sensor
#define S3 4    // S3 pin of color sensor
#define sensorOut 15 // OUT pin of color sensor

// --- LCD Configuration ---
LiquidCrystal_I2C lcd(0x27, 16, 2);

// --- WiFi Credentials ---
#define WIFI_SSID "Suuuiiii"
#define WIFI_PASSWORD "kosasi123"

// --- Connection and Update Intervals ---
#define WIFI_RETRY_DELAY 500
#define MAX_WIFI_RETRIES 20
unsigned long lastWifiCheck = 0;
const long wifiCheckInterval = 30000; // Check WiFi every 30 seconds

unsigned long lastFirebaseCheck = 0;
const long firebaseCheckInterval = 5000; // Update Firebase data every 5 seconds

unsigned long lastBotCheck = 0;
const long BOT_CHECK_INTERVAL = 1000; // Check Telegram messages every 1 second

// --- Telegram Bot Settings ---
// MAKE SURE TO REPLACE with your Telegram Bot Token!
#define BOT_TOKEN "7566286835:AAFDNxIpLPwHuiK0NPkcM8Gaxx4isM9uqEM"
// MAKE SURE TO REPLACE with your Telegram Chat ID! You can get it from the 'getUpdates' API.
// If it's a private chat, the ID is positive. If it's a group, the ID is negative.
#define CHAT_ID "6249178898"
WiFiClientSecure secured_client;
UniversalTelegramBot bot(BOT_TOKEN, secured_client); // Initialize bot object

// --- Firebase Objects ---
FirebaseData firebaseData;
FirebaseAuth auth;
FirebaseConfig config;

// --- Servo Object ---
Servo moneyServo;

// --- Global Variables ---
String nama = "Memuat...";
String kelas = "...";
int currentSaldo = 0;
String lastUpdate = "";
bool firebaseReady = false;
bool studentDataLoaded = false;

// Color sensor readings
int Red = 0;
int Blue = 0;
int Green = 0;

// Money detection status - PERBAIKAN UTAMA DI SINI
bool moneyDetectedFlag = false;
unsigned long lastDetectionTime = 0;        // BARU: Waktu deteksi terakhir
const long detectionCooldown = 2000;        // BARU: Cooldown 2 detik antara deteksi
const long noMoneyThreshold = 1000;         // BARU: Waktu tunggu sebelum reset flag (1 detik)
unsigned long lastNoMoneyTime = 0;          // BARU: Waktu terakhir tidak ada uang terdeteksi

// Servo movement settings
int servoRestPosition = 0;
int servo1kPosition = 60;
int servo2kPosition = 70;
int servo5kPosition = 80;
int servo10kPosition = 90;
unsigned long servoMoveStartTime = 0;
const long servoReturnDelay = 3000;

// --- Function Prototypes ---
void connectToWifi();
void setupNTP();
int getRed();
int getGreen();
int getBlue();
void moveServo(int position);
void displayDataFromFirebase();
String getCurrentDateTime();
bool loadStudentBasicInfo();
bool getStudentDataFromFirebase();
bool addNewTransaction(int amount);
void handleNewMessages(int numNewMessages);
bool isNoMoneyDetected(); // BARU: Fungsi untuk mengecek apakah tidak ada uang

// --- Setup Function ---
void setup() {
    Serial.begin(115200);
    DPRINTLN("\n--- Memulai Detektor Uang ESP32 ---");

    Wire.begin(SDA_PIN, SCL_PIN);
    lcd.init();
    lcd.backlight();

    pinMode(S2, OUTPUT);
    pinMode(S3, OUTPUT);
    pinMode(sensorOut, INPUT);

    moneyServo.attach(SERVO_PIN);
    moveServo(servoRestPosition);

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Detektor Uang");
    lcd.setCursor(0, 1);
    lcd.print("Memulai...");
    delay(1500);

    connectToWifi();

    if (WiFi.status() == WL_CONNECTED) {
        setupNTP();
    }

    randomSeed(analogRead(0));

    // Firebase configuration
    config.api_key = FIREBASE_AUTH;
    config.database_url = DATABASE_URL;
    Firebase.reconnectWiFi(true);

    DPRINTLN("Mendaftar untuk Autentikasi Anonim Firebase...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Auth Firebase...");
    lcd.setCursor(0, 1);
    lcd.print("Mohon tunggu...");

    if (Firebase.signUp(&config, &auth, "", "")) {
        DPRINTLN("Autentikasi Anonim Firebase Berhasil!");
        firebaseReady = true;
    } else {
        DPRINTF("Kesalahan Autentikasi Firebase: %s\n", config.signer.signupError.message.c_str());
        firebaseReady = false;
    }

    Firebase.begin(&config, &auth);
    delay(2000);

    if (Firebase.ready()) {
        DPRINTLN("Firebase siap!");
        firebaseReady = true;

        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Memuat data...");

        if (loadStudentBasicInfo()) {
            DPRINTLN("Berhasil memuat info dasar siswa");
            studentDataLoaded = true;
            if (getStudentDataFromFirebase()) {
                DPRINTLN("Berhasil memuat data lengkap");
            }
        } else {
            DPRINTLN("Gagal memuat data siswa");
            nama = "Tidak Dikenal";
            kelas = "?";
        }

        String startupMsg = "💰 Bot Detektor Uang telah dimulai!\n";
        startupMsg += "👤 Nama: " + nama + "\n";
        startupMsg += "🏫 Kelas: " + kelas + "\n";
        startupMsg += "📊 Saldo Saat Ini: Rp " + String(currentSaldo) + "\n";
        startupMsg += "IP: " + WiFi.localIP().toString();

        DPRINTLN("Mengirim pesan startup Telegram...");
        if (!bot.sendMessage(CHAT_ID, startupMsg, "")) {
            DPRINTLN("Gagal mengirim pesan startup Telegram!");
        } else {
            DPRINTLN("Pesan startup Telegram terkirim.");
        }
    } else {
        DPRINTLN("Inisialisasi Firebase gagal!");
        firebaseReady = false;
    }

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Sistem Siap!");
    delay(1500);

    // Test servo movement
    DPRINTLN("Menguji pergerakan servo...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Menguji Servo...");
    moveServo(servo1kPosition);
    delay(500);
    moveServo(servo2kPosition);
    delay(500);
    moveServo(servo5kPosition);
    delay(500);
    moveServo(servo10kPosition);
    delay(500);
    moveServo(servoRestPosition);
    DPRINTLN("Uji servo selesai.");

    displayDataFromFirebase();
}

// --- Main Loop ---
void loop() {
    unsigned long currentMillis = millis();

    // WiFi connection management
    if (currentMillis - lastWifiCheck >= wifiCheckInterval) {
        lastWifiCheck = currentMillis;
        if (WiFi.status() != WL_CONNECTED) {
            DPRINTLN("Koneksi WiFi terputus. Mencoba menyambung kembali...");
            lcd.clear();
            lcd.setCursor(0, 0);
            lcd.print("Menyambung WiFi..");
            connectToWifi();
            if (WiFi.status() == WL_CONNECTED) {
                setupNTP();
                firebaseReady = Firebase.ready();
                if (firebaseReady) {
                    if (!studentDataLoaded) {
                        loadStudentBasicInfo();
                    }
                    getStudentDataFromFirebase();
                }
            }
        }
    }

    // Firebase data update
    if (WiFi.status() == WL_CONNECTED && Firebase.ready() && firebaseReady &&
        currentMillis - lastFirebaseCheck >= firebaseCheckInterval) {
        lastFirebaseCheck = currentMillis;
        DPRINTLN("Memperbarui data Firebase...");
        if (getStudentDataFromFirebase()) {
            displayDataFromFirebase();
            DPRINTLN("Data Firebase berhasil diperbarui.");
        } else {
            DPRINTLN("Gagal memperbarui data Firebase.");
            lcd.clear();
            lcd.setCursor(0, 0);
            lcd.print("Error Firebase!");
            lcd.setCursor(0, 1);
            lcd.print("Cek DB/Jaringan");
            delay(2000);
            displayDataFromFirebase();
        }
    }

    // Telegram bot polling
    if (WiFi.status() == WL_CONNECTED && currentMillis - lastBotCheck >= BOT_CHECK_INTERVAL) {
        lastBotCheck = currentMillis;
        DPRINTLN("Mengecek pesan Telegram baru...");
        int numNewMessages = bot.getUpdates(bot.last_message_received + 1);
        if (numNewMessages > 0) {
            DPRINTF("Menerima %d pesan Telegram baru\n", numNewMessages);
            handleNewMessages(numNewMessages);
        } else if (numNewMessages < 0) {
            DPRINTLN("Gagal mendapatkan pembaruan Telegram. Mungkin masalah koneksi atau token.");
        } else {
            DPRINTLN("Tidak ada pesan Telegram baru.");
        }
    }

    // Servo return logic
    if (servoMoveStartTime > 0 && currentMillis - servoMoveStartTime >= servoReturnDelay) {
        moveServo(servoRestPosition);
        servoMoveStartTime = 0;
        DPRINTLN("Servo kembali ke posisi istirahat.");
        displayDataFromFirebase();
    }

        // PERBAIKAN UTAMA: Logika deteksi uang yang diperbaiki
    if (servoMoveStartTime == 0) {
        Red = getRed();
        delay(50);
        Green = getGreen();
        delay(50);
        Blue = getBlue();
        delay(50);

        DPRINTF("R: %d, G: %d, B: %d\n", Red, Green, Blue); // Debug RGB values

        if (isNoMoneyDetected()) {
            if (moneyDetectedFlag && (currentMillis - lastNoMoneyTime >= noMoneyThreshold)) {
                DPRINTLN("Reset detection flags - no money detected for sufficient time");
                moneyDetectedFlag = false;
                lastDetectionTime = 0;
            }
            lastNoMoneyTime = currentMillis;
        } else {
            lastNoMoneyTime = currentMillis;
        }

        int detectedAmount = 0;
        String detectedType = "";

        bool canDetect = !moneyDetectedFlag || (currentMillis - lastDetectionTime >= detectionCooldown);

        if (canDetect && !isNoMoneyDetected()) {
            // Rentang deteksi berdasarkan nilai RGB
            if (Red > 27 && Red < 34 && Green > 28 && Green < 34 && Blue > 26 && Blue < 31) {
                detectedAmount = 1000;
                detectedType = "1k";
                moveServo(servo1kPosition);
            } else if (Red > 28 && Red < 32 && Green > 24 && Green < 28 && Blue > 17 && Blue < 20) {
                detectedAmount = 2000;
                detectedType = "2k";
                moveServo(servo2kPosition);
            } else if (Red > 24 && Red < 29 && Green > 26 && Green < 30 && Blue > 21 && Blue < 25) {
                detectedAmount = 5000;
                detectedType = "5k";
                moveServo(servo5kPosition);
            } else if (Red > 38 && Red < 45 && Green > 38 && Green < 45 && Blue > 27 && Blue < 31) {
                detectedAmount = 10000;
                detectedType = "10k";
                moveServo(servo10kPosition);
            }

            if (detectedAmount > 0) {
                DPRINTF("Terdeteksi: Rp %d (%s). Memproses transaksi...\n", detectedAmount, detectedType.c_str());

                moneyDetectedFlag = true;
                lastDetectionTime = currentMillis;
                servoMoveStartTime = currentMillis;

                lcd.clear();
                lcd.setCursor(0, 0);
                lcd.print("Terdeteksi: " + detectedType);
                lcd.setCursor(0, 1);
                lcd.print("Menambah ke DB...");

                bool transactionSuccess = false;

                if (WiFi.status() == WL_CONNECTED && Firebase.ready() && firebaseReady) {
                    transactionSuccess = addNewTransaction(detectedAmount);

                    // Jika gagal, coba ulang sekali
                    if (!transactionSuccess) {
                        delay(500);
                        transactionSuccess = addNewTransaction(detectedAmount);
                    }

                    if (transactionSuccess) {
                        DPRINTLN("Transaksi berhasil ditambahkan.");

                        if (getStudentDataFromFirebase()) {
                            lcd.clear();
                            lcd.setCursor(0, 0);
                            lcd.print("Berhasil! +" + detectedType);
                            lcd.setCursor(0, 1);
                            lcd.print("Total: Rp" + String(currentSaldo));
                            delay(2000);

                            // Kirim pesan ke Telegram (selalu kirim)
                            String message = "✅ Transaksi Baru!\n";
                            message += "👤 " + nama + " (" + kelas + ")\n";
                            message += "💵 Jumlah: Rp " + String(detectedAmount) + "\n";
                            message += "💰 Saldo Baru: Rp " + String(currentSaldo) + "\n";
                            message += "🕒 Waktu: " + getCurrentDateTime();

                            DPRINTLN("Mengirim notifikasi transaksi Telegram...");
                            if (!bot.sendMessage(CHAT_ID, message, "")) {
                                DPRINTLN("Gagal mengirim notifikasi transaksi Telegram!");
                            } else {
                                DPRINTLN("Notifikasi transaksi Telegram terkirim.");
                            }
                        } else {
                            DPRINTLN("Gagal mendapatkan data terbaru dari Firebase setelah transaksi.");
                            lcd.clear();
                            lcd.setCursor(0, 0);
                            lcd.print("Error Update DB");
                            delay(2000);
                        }
                    } else {
                        DPRINTLN("Gagal menambahkan transaksi ke Firebase.");
                        lcd.clear();
                        lcd.setCursor(0, 0);
                        lcd.print("Transaksi Gagal!");
                        delay(2000);
                        moneyDetectedFlag = false; // reset
                    }
                } else {
                    DPRINTLN("Tidak ada koneksi WiFi/Firebase!");
                    lcd.clear();
                    lcd.setCursor(0, 0);
                    lcd.print("Koneksi gagal!");
                    delay(2000);
                    moneyDetectedFlag = false; // reset
                }

                displayDataFromFirebase(); // perbarui tampilan LCD
            }
        }
    }
}
    // BARU: Fungsi untuk mengecek apakah tidak ada uang terdeteksi
    bool isNoMoneyDetected() {
        // Threshold yang lebih realistis - sesuaikan dengan kondisi sensor Anda
        return (Red < 15 && Green < 15 && Blue < 15) || 
            (Red > 200 && Green > 200 && Blue > 200); // Sangat terang atau sangat gelap
    }

    // --- Firebase Related Functions ---
    bool loadStudentBasicInfo() {
        if (WiFi.status() != WL_CONNECTED || !Firebase.ready() || !firebaseReady) {
            DPRINTLN("loadStudentBasicInfo: WiFi tidak terhubung atau Firebase tidak siap.");
            return false;
        }

        String pathNama = "/students/" + studentId + "/nama";
        if (Firebase.RTDB.getString(&firebaseData, pathNama)) {
            if (firebaseData.dataType() == "string") {
                nama = firebaseData.stringData();
                DPRINTLN("Memuat nama: " + nama);
            } else {
                DPRINTLN("'nama' bukan tipe string.");
                nama = "Tidak Dikenal";
            }
        } else {
            DPRINTLN("Gagal mendapatkan 'nama': " + firebaseData.errorReason());
            nama = "Tidak Dikenal";
            return false;
        }

        String pathKelas = "/students/" + studentId + "/kelas";
        if (Firebase.RTDB.getString(&firebaseData, pathKelas)) {
            if (firebaseData.dataType() == "string") {
                kelas = firebaseData.stringData();
                DPRINTLN("Memuat kelas: " + kelas);
            } else {
                DPRINTLN("'kelas' bukan tipe string.");
                kelas = "?";
            }
        } else {
            DPRINTLN("Gagal mendapatkan 'kelas': " + firebaseData.errorReason());
            kelas = "?";
        }
        return true;
    }

    bool addNewTransaction(int amount) {
        if (WiFi.status() != WL_CONNECTED || !Firebase.ready() || !firebaseReady) {
            DPRINTLN("addNewTransaction: WiFi tidak terhubung atau Firebase tidak siap.");
            return false;
        }

        String studentTransactionsPath = "/students/" + studentId + "/transaksi";
        String studentBalancePath = "/students/" + studentId + "/saldo";
        String currentTime = getCurrentDateTime();

        FirebaseJson transactionJson;
        transactionJson.add("jumlah", amount);
        transactionJson.add("tanggal", currentTime);

        DPRINTLN("addNewTransaction: Menambahkan transaksi baru ke Firebase...");
        if (Firebase.RTDB.push(&firebaseData, studentTransactionsPath.c_str(), &transactionJson)) {
            DPRINTLN("Transaksi berhasil ditambahkan dengan ID: " + firebaseData.dataPath());
            
            int newSaldo = currentSaldo + amount;
            DPRINTF("addNewTransaction: Memperbarui saldo ke %d...\n", newSaldo);
            if (Firebase.RTDB.setInt(&firebaseData, studentBalancePath.c_str(), newSaldo)) {
                DPRINTLN("Saldo berhasil diperbarui.");
                currentSaldo = newSaldo;
                return true;
            } else {
                DPRINTLN("Gagal memperbarui saldo: " + firebaseData.errorReason());
                return false;
            }
        } else {
            DPRINTLN("Gagal menambahkan transaksi: " + firebaseData.errorReason());
            return false;
        }
    }

    bool getStudentDataFromFirebase() {
        if (WiFi.status() != WL_CONNECTED || !Firebase.ready() || !firebaseReady) {
            DPRINTLN("getStudentDataFromFirebase: WiFi tidak terhubung atau Firebase tidak siap.");
            return false;
        }

        String studentPath = "/students/" + studentId;

        if (Firebase.RTDB.getInt(&firebaseData, studentPath + "/saldo")) {
            if (firebaseData.dataType() == "int") {
                currentSaldo = firebaseData.intData();
            } else {
                DPRINTLN("'saldo' bukan tipe int. Default ke 0.");
                currentSaldo = 0;
            }
        } else {
            DPRINTLN("Gagal mendapatkan 'saldo': " + firebaseData.errorReason());
            currentSaldo = 0;
        }

        if (Firebase.RTDB.getString(&firebaseData, studentPath + "/last_reset")) {
            if (firebaseData.dataType() == "string") {
                lastUpdate = firebaseData.stringData();
            } else {
                lastUpdate = getCurrentDateTime();
            }
        } else {
            DPRINTLN("Gagal mendapatkan 'last_reset': " + firebaseData.errorReason());
            lastUpdate = getCurrentDateTime();
        }

        if (Firebase.RTDB.getString(&firebaseData, studentPath + "/nama")) {
            if (firebaseData.dataType() == "string") {
                nama = firebaseData.stringData();
            }
        }

        if (Firebase.RTDB.getString(&firebaseData, studentPath + "/kelas")) {
            if (firebaseData.dataType() == "string") {
                kelas = firebaseData.stringData();
            }
        }

        DPRINTF("Dimuat - Saldo: %d, Terakhir Diperbarui: %s, Nama: %s, Kelas: %s\n",
                currentSaldo, lastUpdate.c_str(), nama.c_str(), kelas.c_str());

        return true;
    }

// --- LCD Display Function ---
void displayDataFromFirebase() {
    lcd.clear();
    lcd.setCursor(0, 0);

    String displayName = nama;
    if (displayName.length() > 9) {
        displayName = displayName.substring(0, 9);
    }
    lcd.print(displayName);
    lcd.print(" ");
    lcd.print(kelas);

    lcd.setCursor(0, 1);
    if (WiFi.status() == WL_CONNECTED && Firebase.ready() && firebaseReady) {
        lcd.print("Rp ");
        lcd.print(currentSaldo);
        lcd.print(" [DB]");
    } else {
        lcd.print("DB Offline!");
    }
}

// --- Utility Functions ---
String getCurrentDateTime() {
    time_t now = time(nullptr);
    struct tm *timeinfo = localtime(&now);
    char dateTime[30];
    strftime(dateTime, 30, "%Y-%m-%d %H:%M:%S", timeinfo);
    return String(dateTime);
}

// --- Hardware Control Functions ---
void moveServo(int position) {
    if (position < 0) position = 0;
    if (position > 90) position = 90;
    moneyServo.write(position);
    DPRINTF("Servo bergerak ke posisi: %d derajat\n", position);
}

void connectToWifi() {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Menghubungkan WiFi");

    DPRINT("Menghubungkan ke WiFi SSID: ");
    DPRINTLN(WIFI_SSID);

    WiFi.disconnect(true);
    delay(1000);
    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    secured_client.setInsecure();

    int retries = 0;
    while (WiFi.status() != WL_CONNECTED && retries < MAX_WIFI_RETRIES) {
        delay(WIFI_RETRY_DELAY);
        DPRINT(".");
        lcd.setCursor(retries % 16, 1);
        lcd.print(".");
        retries++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        DPRINT("\nWiFi terhubung. Alamat IP: ");
        DPRINTLN(WiFi.localIP());

        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("WiFi Terhubung!");
        lcd.setCursor(0, 1);
        lcd.print(WiFi.localIP());
        delay(2000);
    } else {
        DPRINTLN("\nGagal terhubung ke WiFi.");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("WiFi Gagal!");
        lcd.setCursor(0, 1);
        lcd.print("Cek kredensial");
        delay(3000);
    }
}

void setupNTP() {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Mendapatkan waktu...");
    DPRINTLN("Menyinkronkan waktu dengan server NTP...");
    configTime(7 * 3600, 0, "pool.ntp.org", "time.nist.gov", "0.id.pool.ntp.org");

    time_t now = time(nullptr);
    int attempts = 0;
    const int maxAttempts = 100;

    while (now < 24 * 3600 && attempts < maxAttempts) {
        delay(100);
        now = time(nullptr);
        attempts++;
    }

    if (now < 24 * 3600) {
        DPRINTLN("Gagal mendapatkan waktu dari server NTP.");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print("Sinkronisasi Waktu Gagal!");
        delay(2000);
    } else {
        DPRINTLN("Waktu berhasil disinkronkan.");
        struct tm timeinfo;
        localtime_r(&now, &timeinfo);
        DPRINTF("Waktu saat ini: %s\n", asctime(&timeinfo));
    }
}

// Color sensor functions
int getRed() {
    digitalWrite(S2, LOW);
    digitalWrite(S3, LOW);
    return pulseIn(sensorOut, LOW);
}

int getGreen() {
    digitalWrite(S2, HIGH);
    digitalWrite(S3, HIGH);
    return pulseIn(sensorOut, LOW);
}

int getBlue() {
    digitalWrite(S2, LOW);
    digitalWrite(S3, HIGH);
    return pulseIn(sensorOut, LOW);
}


// --- Penanganan Pesan Telegram ---
// FUNGSI INI ADALAH JEMBATAN ANTARA PENGGUNA TELEGRAM DAN DATA FIREBASE MELALUI ESP32
void handleNewMessages(int numNewMessages) {
    DPRINTLN("Menangani pesan Telegram baru.");
    for (int i = 0; i < numNewMessages; i++) {
        String chat_id = bot.messages[i].chat_id;
        String text = bot.messages[i].text;

        DPRINTLN("Menerima pesan dari: " + chat_id);
        DPRINTLN("Teks pesan: " + text);

        // Validasi CHAT_ID: Pastikan pesan berasal dari ID yang diizinkan
        // Bot hanya akan merespons CHAT_ID yang terdaftar.
        if (chat_id != CHAT_ID) {
            DPRINTLN("ID obrolan tidak sah: " + chat_id);
            if (!bot.sendMessage(chat_id, "Akses tidak sah. ID obrolan Anda tidak dikenali.", "")) {
                DPRINTLN("Gagal mengirim pesan akses tidak sah!");
            }
            continue; // Lewati pemrosesan pesan ini
        }

        // ESP32 MENGAMBIL DATA DARI FIREBASE (MELALUI fungsi getStudentDataFromFirebase)
        // DAN MENGIRIMNYA KE TELEGRAM
        if (text == "/status") {
            // Pastikan data Firebase terbaru diambil sebelum mengirim
            getStudentDataFromFirebase(); // PENTING: Dapatkan saldo terbaru dari Firebase

            String statusMsg = "📊 Status Saat Ini:\n";
            statusMsg += "👤 Nama: " + nama + "\n";
            statusMsg += "🏫 Kelas: " + kelas + "\n";
            statusMsg += "💰 Saldo: Rp " + String(currentSaldo) + "\n";
            statusMsg += "Pembaruan DB Terakhir: " + lastUpdate + "\n";
            statusMsg += "IP WiFi: " + (WiFi.status() == WL_CONNECTED ? WiFi.localIP().toString() : "Terputus") + "\n";
            statusMsg += "Waktu Sistem: " + getCurrentDateTime();
            
            DPRINTLN("Mengirim pesan status Telegram...");
            if (!bot.sendMessage(chat_id, statusMsg, "")) { // Bot mengirim pesan status ke CHAT_ID
                DPRINTLN("Gagal mengirim pesan status Telegram!");
            } else {
                DPRINTLN("Pesan status Telegram terkirim.");
            }
        } else if (text == "/help") {
            String helpMsg = "Perintah yang tersedia:\n";
            helpMsg += "/status - Dapatkan saldo saat ini dan status sistem.\n";
            helpMsg += "/help - Tampilkan pesan bantuan ini.";
            DPRINTLN("Mengirim pesan bantuan Telegram...");
            if (!bot.sendMessage(chat_id, helpMsg, "")) { // Bot mengirim pesan bantuan
                DPRINTLN("Gagal mengirim pesan bantuan Telegram!");
            } else {
                DPRINTLN("Pesan bantuan Telegram terkirim.");
            }
        } else {
            DPRINTLN("Perintah tidak dikenal. Mengirim pesan balasan.");
            if (!bot.sendMessage(chat_id, "Perintah tidak dikenal. Gunakan /help untuk perintah yang tersedia.", "")) {
                DPRINTLN("Gagal mengirim pesan perintah tidak dikenal!");
            } else {
                DPRINTLN("Pesan perintah tidak dikenal terkirim.");
            }
        }
    }
}

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiManager.h>
#include <Servo.h> // Include the ESP8266 Servo library

Servo myServo; // Create a servo object
int servoPin = 2; // Define the pin connected to the servo motor

#define IR_SENSOR_PIN 4
const char* serverUrl = "http://192.168.0.181:5500/receive.php";
WiFiClient wifiClient;

void setup() {
  Serial.begin(9600);
  pinMode(IR_SENSOR_PIN, INPUT);
  myServo.attach(servoPin); // Attach the servo to the GPIO pin
  Serial.println("Servo Control Initialized!");
  myServo.write(0); 
  delay(500);
  WiFiManager wifiManager;

  if (!wifiManager.autoConnect("Pet Door", "password")) {
        Serial.println("Failed to connect to WiFi. Starting AP mode...");
        delay(3000); // Wait for debugging message to be sent
        ESP.restart(); // Restart the ESP to retry or continue in AP mode
  }
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi disconnected. Restarting AP mode...");
        WiFiManager wifiManager;
        wifiManager.startConfigPortal("Pet Door", "password"); // Manually start AP
  }
  int sensorState = digitalRead(IR_SENSOR_PIN);


  if (sensorState == LOW) {
    myServo.write(0); 
    sendLog();
    delay(5000);
  } else {
    myServo.write(180);
  }

  delay(500);
}

void sendLog() {
  if (WiFi.status() == WL_CONNECTED) {
     HTTPClient http;                       

    http.begin(wifiClient, serverUrl);
    
    String postData = "device=petdoor34";
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      Serial.println("Data sent successfully! " + postData);
    } else {
      Serial.println("Error sending data");
      Serial.println("HTTP Response Code: " + String(httpResponseCode));
    }
    http.end();
  } else {
    Serial.println("Wi-Fi not connected");
  }
}
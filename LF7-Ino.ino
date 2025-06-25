#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_NeoPixel.h>
#include <ArduinoJson.h>

#define LED_PIN 14
#define NUM_LEDS 12

const char* ssid = "MikroTik-AF2E92";
const char* apiUrl = "http://192.168.88.103/LF7-Webserver/LF7-Webserver/api.php";

Adafruit_NeoPixel strip(NUM_LEDS, LED_PIN, NEO_GRB + NEO_KHZ800);

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid);

  Serial.print("Verbindung wird aufgebaut");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n WLAN verbunden!");
  strip.begin();
  strip.show();
}

void resetLEDs() {
  for (int i = 0; i < NUM_LEDS; i++) {
    strip.setPixelColor(i, 0, 0, 0);
  }
  strip.show();
}

void showLED(String priority) {
  resetLEDs();
  uint32_t color = strip.Color(0, 0, 0);

  if (priority == "high") {
    color = strip.Color(255, 0, 0);     // Rot
  } else if (priority == "medium") {
    color = strip.Color(255, 255, 0);   // Gelb
  } else if (priority == "low") {
    color = strip.Color(0, 255, 0);     // Grün
  }

  for (int i = 0; i < NUM_LEDS; i++) {
    strip.setPixelColor(i, color);
  }
  strip.show();
}

int getPrioValue(const String& prio) {
  if (prio == "low") return 1;
  if (prio == "medium") return 2;
  if (prio == "high") return 3;
  return 0;  // unbekannt
}

String fetchPriority() {
  HTTPClient http;
  http.begin(apiUrl);
  int httpCode = http.GET();

  if (httpCode == 200) {
    String payload = http.getString();
    http.end();

    Serial.println("JSON Antwort empfangen:");
    Serial.println(payload);

    DynamicJsonDocument doc(4096);
    DeserializationError error = deserializeJson(doc, payload);

    if (error) {
      Serial.print("Fehler beim Parsen: ");
      Serial.println(error.c_str());
      return "low";
    }

    if (!doc.is<JsonArray>()) {
      Serial.println("JSON ist kein Array – prüfe Struktur!");
      return "low";
    }

    int best = 0;
    String bestPrio = "low";

    for (JsonObject task : doc.as<JsonArray>()) {
      String status = task["status"] | "";
      String priority = task["priority"] | "";

      Serial.print("→ Task: Status = ");
      Serial.print(status);
      Serial.print(", Priority = ");
      Serial.println(priority);

      if (status != "erledigt") {
        int prioVal = getPrioValue(priority);
        if (prioVal > best) {  // Jetzt höher = wichtiger
          best = prioVal;
          bestPrio = priority;
        }
      }
    }

    return bestPrio;
  }

  Serial.print("Fehler beim HTTP-Request: Code ");
  Serial.println(httpCode);
  http.end();
  return "high";
}

void loop() {
  String prio = fetchPriority();
  Serial.print("Aktuelle höchste Priorität: ");
  Serial.println(prio);
  showLED(prio);
  delay(10000);
}

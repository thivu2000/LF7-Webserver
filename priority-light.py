import network
import urequests
from machine import Pin
from time import sleep

def connect_wifi(ssid, password):
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    wlan.connect(ssid, password)

    while not wlan.isconnected():
        sleep(0.5)
        print(".", end="")

red = Pin(4, Pin.OUT)
yellow = Pin(5, Pin.OUT)
green = Pin(6, Pin.OUT)

API_URL = "http://192.168.X.X:63342/LF7-Webserver/LF7-Webserver/api.php"
# ↑ Ersetze 192.168.X.X durch die lokale IP-Adresse deines PCs

def reset_leds():
    red.off()
    yellow.off()
    green.off()

def show_led(priority):
    reset_leds()
    if priority == "high":
        red.on()
    elif priority == "medium":
        yellow.on()
    else:
        green.on()

def fetch_priority():
    try:
        response = urequests.get(API_URL)
        tasks = response.json()
        response.close()

        order = {"high": 1, "medium": 2, "low": 3}
        lowest = 3
        for task in tasks:
            if task["status"] != "erledigt":
                prio = order.get(task["priority"], 3)
                if prio < lowest:
                    lowest = prio

        return {1: "high", 2: "medium", 3: "low"}[lowest]

    except Exception as e:
        return "low"

connect_wifi("DEIN_WIFI_NAME", "DEIN_PASSWORT")

while True:
    prio = fetch_priority()
    print("Aktuelle Priorität:", prio)
    show_led(prio)
    sleep(10)

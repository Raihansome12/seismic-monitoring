import paho.mqtt.client as mqtt
import json
import time
import random
import numpy as np
from datetime import datetime, timezone
import ssl
import os

# MQTT Configuration from .env
MQTT_BROKER = "240fa9ad32cb44929936ab704925afa5.s1.eu.hivemq.cloud"
MQTT_PORT = 8883
MQTT_USERNAME = "project-kuliah"
MQTT_PASSWORD = "Raihan@3012"
MQTT_CLIENT_ID = "python-simulator"
TOPIC = "sensors/geophone"

# TLS Configuration
CA_CERT_PATH = os.path.join(os.path.dirname(__file__), "..", "storage", "app", "certificates", "isrgrootx1.pem")

def generate_seismic_data(samples=5):
    """Generate simulated seismic data with alternating positive/negative integer values between 100-1000"""
    adc_counts = []
    base_value = random.randint(100, 1000)  # Start with a random base value
    
    for i in range(samples):
        # Alternate between positive and negative
        if i % 2 == 0:
            value = base_value + random.randint(-50, 50)  # Add some variation
        else:
            value = -(base_value + random.randint(-50, 50))
        
        # Ensure value stays within 100-1000 range
        value = max(10, min(10000, abs(value)))
        if i % 2 == 1:  # Make negative for odd indices
            value = -value
            
        adc_counts.append(int(value))
    
    return adc_counts

def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("Connected to MQTT Broker!")
    else:
        print(f"Failed to connect, return code {rc}")

def on_disconnect(client, userdata, rc):
    print(f"Disconnected with result code {rc}")

def main():
    # Create MQTT client
    client = mqtt.Client(client_id=MQTT_CLIENT_ID)
    client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)
    
    # Disable certificate verification for development
    print("Warning: Certificate verification is disabled for development testing")
    client.tls_set(cert_reqs=ssl.CERT_NONE)
    
    # Set callback functions
    client.on_connect = on_connect
    client.on_disconnect = on_disconnect
    
    try:
        # Connect to MQTT broker
        print(f"Connecting to {MQTT_BROKER}...")
        client.connect(MQTT_BROKER, MQTT_PORT, 60)
        client.loop_start()
        
        print("Starting MQTT data simulator...")
        print(f"Publishing to topic: {TOPIC}")
        print("Configuration:")
        print("- 5 data points per payload")
        print("- 50 samples per second (SPS)")
        print("- 100ms interval between payloads")
        print("- Values between 100-1000 with alternating signs")
        print("Press Ctrl+C to stop")
        
        while True:
            # Generate simulated data
            adc_counts = generate_seismic_data(samples=25)
            reading_times = datetime.utcnow().isoformat(timespec='milliseconds')
            
            # Create payload
            payload = {
                "adc_counts": adc_counts,
                "reading_times": reading_times
            }
            
            # Publish to MQTT
            client.publish(TOPIC, json.dumps(payload))
            print(f"Published data at {reading_times}: {adc_counts}")
            
            # Wait for next payload (array 25 data (50 sos) = 500ms)
            time.sleep(0.5)
            
    except KeyboardInterrupt:
        print("\nStopping simulator...")
        client.loop_stop()
        client.disconnect()
    except Exception as e:
        print(f"Error: {e}")
        client.loop_stop()
        client.disconnect()

if __name__ == "__main__":
    main() 
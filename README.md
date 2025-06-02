# 8-a-M
## First Commit

Final Year Project: Real-Time Delivery Management System with Smart Tracking

## Project Description
This project aims to develop an optimized web platform for logistics management, integrating advanced real-time tracking features and delivery route optimization. The system addresses three key challenges:
- Reducing failed deliveries through proactive monitoring
- Improving transparency with real-time notifications
- Optimizing logistics costs via self-adjusting delivery routes

## Key Features

### 1. Failed Delivery Management
- Smart reporting by couriers (with evidence capture)
- Knowledge base of failure reasons (future ML analysis)
- Automated re-delivery workflow

### 2. Geolocation Monitoring
- Real-time tracking via Mercure Protocol (+ GPS history)
- Alert system when:
  - A courier deviates >500m from route
  - A package remains inactive >2 hours
- Interactive mapping (Leaflet.js + OpenStreetMap)

### 3. Package Traceability
- Real-time status (QR code scanning at each step)
- Push notifications to customers:
  - "Your package is 15 minutes away"
  - "Delivery postponed (reason: incomplete address)"
- Customer portal with delivery history

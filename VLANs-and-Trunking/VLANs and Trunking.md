# 🌐 Konfigurasi VLAN dan Trunking (Pengembangan Topologi)

## 🧩 Deskripsi
Proyek ini merupakan tugas akhir praktikum **“Configure VLANs and Trunking - Physical Mode”** yang dikembangkan dari modul Cisco NetAcad.  
Pengembangan dilakukan dengan menambahkan **satu switch baru (S3)** dan **satu VLAN tambahan (VLAN 30 - Finance)** untuk memperluas jaringan, namun tetap mempertahankan struktur dan metode konfigurasi dasar.

---

## 🖥️ Topologi Jaringan
![Network Topology](A_2D_digital_network_diagram_illustrates_VLANs_(Vi.png)

**Daftar Perangkat:**
- Switch: `S1`, `S2`, `S3`  
- PC: `PC-A`, `PC-B`, `PC-C`  
- Koneksi antar switch menggunakan **trunk link**  
- VLAN tambahan: `VLAN 30` (Finance)

---

## 📋 Tabel Alamat IP

| Device | Interface | IP Address | Subnet Mask | VLAN | Keterangan |
|:--------|:-----------|:------------|:-------------|:------|:-------------|
| S1 | VLAN 99 | 192.168.1.11 | 255.255.255.0 | 99 | Management |
| S2 | VLAN 99 | 192.168.1.12 | 255.255.255.0 | 99 | Management |
| S3 | VLAN 99 | 192.168.1.13 | 255.255.255.0 | 99 | Management |
| PC-A | NIC | 192.168.10.3 | 255.255.255.0 | 10 | Operations |
| PC-B | NIC | 192.168.10.4 | 255.255.255.0 | 10 | Operations |
| PC-C | NIC | 192.168.30.5 | 255.255.255.0 | 30 | Finance |

---

## ⚙️ Langkah Konfigurasi

### 1️⃣ Konfigurasi Dasar Switch
```bash
enable
configure terminal
hostname S1
enable secret class
line console 0
 password cisco
 login
line vty 0 4
 password cisco
 login
service password-encryption
banner motd $ Authorized Users Only! $
interface vlan 1
 ip address 192.168.1.11 255.255.255.0
 no shutdown
exit
copy running-config startup-config

---

## 2️⃣ Membuat VLAN
Gunakan perintah berikut untuk membuat VLAN di setiap switch.

```bash
vlan 10
 name Operations
vlan 20
 name Parking_Lot
vlan 30
 name Finance
vlan 99
 name Management
vlan 1000
 name Native

---

## 3️⃣ Menghubungkan Port ke VLAN

Setiap PC dihubungkan ke port switch yang sesuai dengan VLAN-nya.

---

### 🟦 S1 (PC-A - VLAN 10)
```bash
interface f0/6
 switchport mode access
 switchport access vlan 10

! Konfigurasi untuk S2
interface f0/18
 switchport mode access
 switchport access vlan 10

! Konfigurasi untuk S3
interface f0/2
 switchport mode access
 switchport access vlan 30

show vlan brief

4️⃣ Konfigurasi VLAN Management

VLAN 99 digunakan untuk manajemen switch. Hapus IP dari VLAN 1 dan pindahkan ke VLAN 99.

interface vlan 1
 no ip address
interface vlan 99
 ip address 192.168.1.11 255.255.255.0
 no shutdown
exit

5️⃣ Konfigurasi Trunk Antar Switch

Gunakan port FastEthernet0/1 untuk trunk antar switch.

interface f0/1
 switchport mode trunk
 switchport trunk native vlan 1000


Lakukan pada S1 ↔ S2 dan S2 ↔ S3.

6️⃣ Verifikasi VLAN dan Trunk

Gunakan perintah berikut untuk memastikan konfigurasi VLAN dan trunk sudah benar:

show vlan brief
show interfaces trunk

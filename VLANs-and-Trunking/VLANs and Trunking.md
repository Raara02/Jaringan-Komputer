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

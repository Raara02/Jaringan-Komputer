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

## ⚙️ Tahapan Konfigurasi

### 1️⃣ Konfigurasi Dasar Switch
Lakukan konfigurasi dasar di setiap switch:

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
```

---

### 2️⃣ Membuat VLAN

Gunakan perintah berikut di setiap switch untuk membuat VLAN.

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
```

---

### 3️⃣ Menghubungkan Port ke VLAN

Setiap PC dihubungkan ke port switch yang sesuai dengan VLAN-nya.

#### 🟦 S1 (PC-A - VLAN 10)

```bash
interface f0/6
 switchport mode access
 switchport access vlan 10
```

#### 🟩 S2 (PC-B - VLAN 10)

```bash
interface f0/18
 switchport mode access
 switchport access vlan 10
```

#### 🟧 S3 (PC-C - VLAN 30)

```bash
interface f0/2
 switchport mode access
 switchport access vlan 30
```

Verifikasi dengan:

```bash
show vlan brief
```

---

### 4️⃣ Konfigurasi VLAN Management

VLAN 99 digunakan untuk manajemen switch. Hapus IP dari VLAN 1 dan pindahkan ke VLAN 99.

```bash
interface vlan 1
 no ip address
interface vlan 99
 ip address 192.168.1.11 255.255.255.0
 no shutdown
exit
```

> Gunakan IP yang berbeda di setiap switch (lihat tabel IP).

---

### 5️⃣ Konfigurasi Trunk Antar Switch

Gunakan port **FastEthernet0/1** untuk trunk antar switch.

```bash
interface f0/1
 switchport mode trunk
 switchport trunk native vlan 1000
```

Lakukan pada S1 ↔ S2 dan S2 ↔ S3.

---

### 6️⃣ Verifikasi VLAN dan Trunk

Gunakan perintah berikut untuk memastikan konfigurasi VLAN dan trunk sudah benar.

```bash
show vlan brief
show interfaces trunk
```

---

### 7️⃣ Uji Konektivitas

Lakukan pengujian konektivitas antar perangkat menggunakan perintah `ping`.

| Pengujian       | Hasil | Keterangan                   |
| :-------------- | :---- | :--------------------------- |
| PC-A ↔ PC-B     | ✅     | VLAN 10 melewati trunk       |
| PC-A ↔ PC-C     | ❌     | Berbeda VLAN                 |
| S1 ↔ S2 ↔ S3    | ✅     | Trunk aktif                  |
| Ping antar VLAN | ❌     | Tidak ada inter-VLAN routing |

> ✅ Berhasil: perangkat berada dalam VLAN yang sama.
> ❌ Gagal: komunikasi antar VLAN diblokir karena belum ada router.

---

## 💾 Menyimpan Konfigurasi

Pastikan konfigurasi disimpan agar tidak hilang setelah restart.

```bash
copy running-config startup-config
```

---

## 🔍 Hasil dan Analisis

* VLAN 10 dan VLAN 30 berhasil dibuat dan terisolasi.
* Trunk antar switch berjalan dengan native VLAN 1000.
* Komunikasi dalam VLAN berhasil, antar VLAN gagal (karena tidak ada inter-VLAN routing).
* VLAN 99 berfungsi sebagai jaringan manajemen.

---

## 🧠 Kesimpulan

Pengembangan topologi ini menambah **switch (S3)** dan **VLAN baru (VLAN 30 - Finance)** untuk memperluas jaringan tanpa mengubah struktur dasar.
Konfigurasi VLAN dan trunking memungkinkan pemisahan domain broadcast dan efisiensi komunikasi antar switch.

---

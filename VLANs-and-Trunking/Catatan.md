

## ⚙️ KONDISI JARINGAN KAMU SAAT INI

Kita lihat **dari konfigurasi di modul:**

| Perangkat         | VLAN    | IP Address   | Fungsi                       |
| ----------------- | ------- | ------------ | ---------------------------- |
| **PC-A**          | VLAN 10 | 192.168.10.3 | Host Operasional di Switch 1 |
| **PC-B**          | VLAN 10 | 192.168.10.4 | Host Operasional di Switch 2 |
| **S1 (Switch 1)** | VLAN 99 | 192.168.1.11 | Switch Management            |
| **S2 (Switch 2)** | VLAN 99 | 192.168.1.12 | Switch Management            |

> 🔸 PC-A dan PC-B berada di **VLAN 10**
> 🔸 Switch S1 dan S2 berada di **VLAN 99**

---

## 🧠 SEKARANG, PAHAM DULU TENTANG “VLAN”

VLAN (Virtual LAN) = **pemisahan jaringan logis** di dalam satu switch.
Artinya, VLAN seperti **tembok pembatas** di dalam jaringan Layer 2 (data-link).

➡️ **Setiap VLAN = 1 broadcast domain.**
Artinya:

* PC di VLAN 10 hanya bisa bicara dengan VLAN 10.
* Perangkat di VLAN 99 hanya bisa bicara dengan VLAN 99.
* Tidak bisa saling berkomunikasi **tanpa router**.

---

## 💬 LALU MENGAPA...

> “PC-A tidak bisa ping Switch 1 (S1),
> tapi bisa ping PC-B?”

Mari kita lihat jalurnya satu per satu ⤵️

---

### 🧩 **1️⃣ Ping PC-A ke Switch 1**

🔹 **PC-A**: IP 192.168.10.3 → VLAN 10
🔹 **S1**: IP 192.168.1.11 → VLAN 99

📉 **Hasil:** ❌ *Ping gagal*
🧾 **Alasan:**

> PC-A (VLAN 10) dan Switch S1 (VLAN 99) berada di VLAN berbeda, jadi **tidak ada jalur Layer 2** yang menghubungkan mereka.
> VLAN 10 tidak bisa “menembus” VLAN 99.

Untuk membuat mereka bisa ping, butuh **router atau Layer 3 switch** yang melakukan **inter-VLAN routing**.

---

### 🧩 **2️⃣ Ping PC-A ke PC-B**

🔹 **PC-A (S1 – VLAN 10)**
🔹 **PC-B (S2 – VLAN 10)**

📈 **Hasil:** ✅ *Ping berhasil*
🧾 **Alasan:**

> Keduanya sama-sama berada di **VLAN 10**, dan port antar-switch (F0/1) sudah dibuat **trunk**, yang membawa VLAN 10 melewati kabel antar-switch.

Ilustrasi sederhananya:

```
PC-A (VLAN 10)
   │
 [S1]───(trunk: VLAN 10 lewat sini)───[S2]
   │
PC-B (VLAN 10)
```

Jadi, meskipun PC-A dan PC-B ada di switch berbeda, **mereka masih dalam VLAN yang sama** → bisa ping sukses.
```

Di sinilah **kenapa PC-A dan PC-B bisa ping** walau beda switch:
karena **trunk mengizinkan VLAN 10 lewat antar-switch**.

Tanpa trunk, VLAN 10 di S1 tidak akan pernah “sampai” ke S2.
Jadi kalau port antar-switch masih mode “access VLAN 1”, ping pasti gagal.


---

### 🧩 **3️⃣ Ping Switch 1 ke Switch 2**

🔹 **S1** IP: 192.168.1.11 → VLAN 99
🔹 **S2** IP: 192.168.1.12 → VLAN 99

📈 **Hasil:** ✅ *Ping berhasil*
🧾 **Alasan:**

> VLAN 99 juga ikut lewat trunk, jadi kedua switch bisa saling komunikasi melalui VLAN 99.

Ilustrasi:

```
S1 (VLAN 99)
   │
[Trunk Link VLAN 99 lewat sini]
   │
S2 (VLAN 99)
```

---

## 🔍 SEDERHANANYA:

| Asal | Tujuan | VLAN Sumber | VLAN Tujuan | Bisa Ping? | Kenapa                           |
| ---- | ------ | ----------- | ----------- | ---------- | -------------------------------- |
| PC-A | PC-B   | 10          | 10          | ✅          | VLAN sama, dilewatkan trunk      |
| PC-A | S1     | 10          | 99          | ❌          | VLAN berbeda (tidak ada routing) |
| S1   | S2     | 99          | 99          | ✅          | VLAN sama, dilewatkan trunk      |
| PC-B | S2     | 10          | 99          | ❌          | VLAN berbeda (tidak ada routing) |

---

## 💡 KESIMPULAN BESAR

➡️ **Ping hanya bisa terjadi di dalam VLAN yang sama** (Layer 2).
➡️ **Switch tidak otomatis menghubungkan antar-VLAN** (butuh router).
➡️ **Trunk** hanyalah jalur agar banyak VLAN lewat satu kabel, bukan penghubung antar VLAN.
➡️ Jadi:

* PC-A dan PC-B bisa ping karena mereka **satu VLAN (10)**.
* Switch 1 dan Switch 2 bisa ping karena mereka **satu VLAN (99)**.
* Tapi PC ke Switch tidak bisa ping karena **berbeda VLAN (10 ≠ 99)**.

---

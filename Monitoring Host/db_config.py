import mysql.connector

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        user="root",       # ganti jika bukan root
        password="",       # isi password MySQL kamu jika ada
        database="monitoring_website"
    )

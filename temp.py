import mysql.connector
from sense_hat import SenseHat
import datetime
import time

sense = SenseHat()

# Connecten met database, host computer
mydb = mysql.connector.connect(
    host= "169.254.182.18", # Om te testen voer eigen IP-adres in
    user="root",
    password="",
    port="3306",
    database="nerdygadgets"
)

cursor = mydb.cursor()
while True:
    temp = round(sense.get_temperature(), 2);
    curentTime = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S");

    update_temp = 'UPDATE coldroomtemperatures SET RecordedWhen = %s, Temperature = %s, ValidFrom = %s WHERE coldRoomSensorNumber = 5';
    val_for_update_temp = (curentTime, temp, curentTime)

    cursor.execute(update_temp, val_for_update_temp)
    mydb.commit()
    
    print(temp)
    time.sleep(3)


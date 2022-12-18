# import modules
from sense_hat import SenseHat
from time import sleep
# object instantiation
sense = SenseHat()
# delay between calls
delay = 5
while True:
    temp = round(sense.get_temperature(), 1)
    print("Temperatuur: %sC" % temp)
    sleep(delay)
print("Einde script")
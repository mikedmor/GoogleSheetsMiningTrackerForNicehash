# Google Sheets Mining Tracker for NiceHash

This repositiory contains all the files I used to put together my Google Sheets Mining Tracker.
In order to use this correctly you will need a webserver that supports php, cron, and mysql.

Alternatively with some tweaks the ImportJSON.gs script you could also use a paid source for the same data.

## How to Setup
**⚠️Important:** Before starting these steps assume you have a webserver that supports php, cron and mysql. If you do not and plan to modify ImportJSON.gs instead to use another service, please skip to step **5**.

1. Copy **config.php**, **copyPrice.php**, **getHistory.php** onto your webserver.
2. Edit **config.php** with your mysql and cryptocompare api key.
    - You can signup for a free account at https://www.cryptocompare.com/ then create a key.
    - Setup other CryptoCompare settings including pairs you want copies to your mysql table, currency type, and exchange.
3. Import the **crypto.sql** file into your mysql database. If you search for data repositories on the internet you should be able to find some documents that can easily be uploaded to the stat_history table with minimal work.
4. Setup a cron job that runs **copyPrice.php** every hour to copy price data to your data from cryptocompare. This will be your historical database. If you would like to copy more often, you will need to edit the **copyPrice.php** script to account for that.
5. Visit https://docs.google.com/spreadsheets/d/1WMX_45F43w6nT2oQ-GyXAcLeo1qO2rTnsSeeyqjhTaU/edit?usp=sharing and click **File -> Make a Copy**
6. Go to the Settings Workbook and update the information
    - Set the domain and directory you are hosting the **getHistory.php** file (Skip this step if you are editing ImportJSON.gs)
    - Set the Coinpair you want to you and how far back you want to load data from (Skip this step if you are editing ImportJSON.gs)
    - Set the NICEHASH API KEY, NICEHASH SECRET KEY, and NICEHASH ORGANIZATION
7. If you are hosting the **getHistory.php** and not editing ImportJSON.gs then you are now complete! Continue reading otherwise.
8. Go to **Tools -> Script Editor** and open the **ImportJSON.gs** file
9. Edit the ImportJSON.gs to work for your service of choice.
    - Depending on how much you change IMPORTJSON function you may need to also update the triggerAutoRefresh.gs, and the PriceHistory workbook at cell A2.

## Post-setup
Now you should have a google spreadsheet setup with all your data, if you need to refresh it there should be a menu option **External Resources -> Refresh**. You can also setup a trigger to run the **triggerAutoRefresh()** function every hour.

## License

Copyright (C) 2021 Mikedmor

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

Blockchain Walker
=================

---
### What is it?

Blockchain Walker walk over Bitcoin blockchain and callback user class for each blockchain event.

Currently Blockchain Walker imports Bitcoin blockchain into HM4 Database, but it can be changed to do other things.
For example it is fearyly easy to import data into MySQL.

---
### How to use

First you need to have bitcoind running with "-txindex=1".
You need to wait about 24 to 48 hours so bitcoind download full blockchain.

Second open "start.php" and make necessary changes.

Third, just use "php start.php".

---
### Observations at 2017-09

Blockchain data will be about 160 GB on disk.

On rotational HDD, full Bitcoin blockchain can be imported into HM4 for about 6 days and data on disk will be about 400 GB.
Most of this time is spend of bitcoind.

---
### What is HM4

HM4 is very fast key value database with Redis interface.

https://github.com/nmmmnu/HM4

---
### Locating the balance of Bitcoin address

Here are how keys looks like if we want to find the balance for address "1EoMoJK3FJPHg4EwrP31zPVu4iLqmCtQ6":
```
127.0.0.1:2000> HGETALL a:1EoMoJK3FJPHg4EwrP31zPVu4iLqmCtQ6
 1) "a:1EoMoJK3FJPHg4EwrP31zPVu4iLqmCtQ6:28c76c9d89f23c1b1f5435d8f4bb5cc66e6cb9d0798f1f0f4293faaac88fb7d0.0"
 2) "0.01270199"
```
Result is as follows:
```
Funding transaction:
28c76c9d89f23c1b1f5435d8f4bb5cc66e6cb9d0798f1f0f4293faaac88fb7d0, output 0
Value:
0.01270199
```

Then we need to check if this is spent, so we check each input as follows:
```
127.0.0.1:2000> get t:28c76c9d89f23c1b1f5435d8f4bb5cc66e6cb9d0798f1f0f4293faaac88fb7d0.0:s
"055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8"
```
Result is as follows:
```
Transaction where input is spent:
055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8
```

As each output is spent this means the address 1EoMoJK3FJPHg4EwrP31zPVu4iLqmCtQ6 have balance of zero.

### List transaction

In case we want to check / list a transaction "055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8", we can do:
```
127.0.0.1:2000> HGETALL t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8
 1) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.-:i:28c76c9d89f23c1b1f5435d8f4bb5cc66e6cb9d0798f1f0f4293faaac88fb7d0.0"
 2) "1"
 3) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.-:i:39edb8741b701b6da2dbc4e02290e8e78cba244bdbad96da203e41ee2704c525.0"
 4) "1"
 5) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.0:o"
 6) "1Kj76Sxe8c3UK85RAQwwdqScAxaBwAY2eb:0.00500000"
 7) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.0:s"
 8) "49f8bd582439a3b2351f92e0fdb5fcb1032acd42e3ab469c16d805627889ce14"
 9) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.1:o"
10) "14yAJga4ZkULbaMz4LUW5vj8GhQYyzCPoW:0.01118727"
11) "t:055bd8148143c5b05bc2808ccafe54be43b292381449d41cc23462d02d3f85d8.1:s"
12) "edf55ace1396229c7a91a4b21cd63293c631ae2ddcc5987402b04ed540b9e5d8"
```

Results
```
Inputs: "t:xxxxx.-:i:xxxx".
Outputs: "t:xxxxx.N:o"
Spent indicators: "t:xxxxx.N:s"
```


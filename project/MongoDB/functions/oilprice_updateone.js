db.oilprice.updateOne(
    { "year": 2018, "month":1 },     
    { 
        $set: { "date": "2018-1" }            
    }
)

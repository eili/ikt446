db.export.aggregate([
    {
        $lookup: {
            from: "currency",
            localField: "date",
            foreignField: "date",
            as: "fromCurr"
        }
    },    
    {
        $replaceRoot: { newRoot: { $mergeObjects: [ { $arrayElemAt: [ "$fromCurr", 0]}, "$$ROOT" ] }}
    },    
    { $project: { 
        fromCurr: 0
    }},
    { $out: "fact1" }
])


db.export.aggregate([
    {
        $lookup: {
            from: "oilprice",
            localField: "date",
            foreignField: "date",
            as: "fromOilprice"
        }
    },    
    {
        $replaceRoot: { newRoot: { $mergeObjects: [ { $arrayElemAt: [ "$fromOilprice", 0]}, "$$ROOT" ] }}
    },    
    { $project: { 
        fromOilprice: 0
    }},
    { $out: "fact2" }
])

db.fact1.aggregate([
    {
        $lookup: {
            from: "fact2",
            localField: "date",
            foreignField: "date",
            as: "fromfact2"
        }
    },    
    {
        $replaceRoot: { newRoot: { $mergeObjects: [ { $arrayElemAt: [ "$fromfact2", 0]}, "$$ROOT" ] }}
    },    
    { $project: { 
        fromfact2: 0
    }},
    { $out: "fact" }
])

db.fact.aggregate( [ 
    { $match: { year: 2017 } }, 
    { $group: { _id: "$year", sum: { $sum: "$amount" } } } 
    ] );
    

db.fact.aggregate([    
    {    $group: {
            _id: "$year", 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }    
    }    
]);    

db.fact.aggregate([
    { $match: { product: "Crude" } }, 
    { $group: {
            _id: {date: "$date"}, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $out: "factaggr"}
]);  


db.fact.aggregate([
    { $match: { product: "Crude",  kbarrels: {$ne: NaN} } }, 
    { $group: {
            _id:  { date: "$date", year: "$year", month: "$month"}, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $project: { year: "$_id.year", month: "$_id.month", date: "$_id.date", amountMNOK: "$amountMNOK", amountMusd: "$amountMusd", kbarrels: "$kbarrels", _id: 0 }},
    { $out: "factAggrByDate"}
]);  


db.fact.aggregate([
    { $match: { product: "Crude", kbarrels: {$ne: NaN} } }, 
    { $group: {
            _id:  { countrycode: "$countrycode", countryname: "$countryname", year: "$year" }, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $project: { countryname: "$_id.countrycode", countryname: "$_id.countryname", year: "$_id.year",
      amountMNOK: "$amountMNOK", amountMusd: "$amountMusd", kbarrels: "$kbarrels", _id: 0 }},
      { $out: "factAggrByCountry"}    
]);  
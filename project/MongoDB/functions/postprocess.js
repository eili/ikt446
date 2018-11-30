<<<<<<< HEAD
db.currency.find().forEach(function(doc) {
    db.currency.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
db.oilprice.find().forEach(function(doc) {
    db.oilprice.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
db.export.find().forEach(function(doc) {
    db.export.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
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
]);
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
]);
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
]);
db.fact.aggregate([
    { $match: { product: "crude" } }, 
    { $group: {
            _id: {date: "$date"}, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $out: "factaggr"}
]);  
db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "amountMusd": doc.amount / doc.currency
        }     
    })
});
db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "kbarrels": 1000 * doc.amount / (doc.currency * doc.value)
        }     
    })
});
db.fact.aggregate([
    { $match: { product: "crude",  kbarrels: {$ne: NaN} } }, 
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
    { $match: { product: "crude", kbarrels: {$ne: NaN} } }, 
    { $group: {
            _id:  { cc: "$cc", year: "$year" }, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $project: { countrycode: "$_id.cc", year: "$_id.year",
      amountMNOK: "$amountMNOK", amountMusd: "$amountMusd", kbarrels: "$kbarrels", _id: 0 }},
      { $out: "factAggrByCountry"}    
=======
db.currency.find().forEach(function(doc) {
    db.currency.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
db.oilprice.find().forEach(function(doc) {
    db.oilprice.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
db.export.find().forEach(function(doc) {
    db.export.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
});
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
]);
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
]);
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
]);
db.fact.aggregate([
    { $match: { product: "crude" } }, 
    { $group: {
            _id: {date: "$date"}, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $out: "factaggr"}
]);  
db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "amountMusd": doc.amount / doc.currency
        }     
    })
});
db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "kbarrels": 1000 * doc.amount / (doc.currency * doc.value)
        }     
    })
});
db.fact.aggregate([
    { $match: { product: "crude",  kbarrels: {$ne: NaN} } }, 
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
    { $match: { product: "crude", kbarrels: {$ne: NaN} } }, 
    { $group: {
            _id:  { cc: "$cc", year: "$year" }, 
            amountMNOK: { $sum: "$amount" },
            amountMusd: { $sum: "$amountMusd" },
            kbarrels: { $sum: "$kbarrels" }
        }
    },
    { $project: { countrycode: "$_id.cc", year: "$_id.year",
      amountMNOK: "$amountMNOK", amountMusd: "$amountMusd", kbarrels: "$kbarrels", _id: 0 }},
      { $out: "factAggrByCountry"}    
>>>>>>> e5815a78e33eefd33852ddbee561c455017c7e38
]);  
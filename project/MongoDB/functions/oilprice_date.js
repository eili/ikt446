db.oilprice.find().forEach(function(doc) {
    db.oilprice.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
})
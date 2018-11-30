db.currency.find().forEach(function(doc) {
    db.currency.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
})
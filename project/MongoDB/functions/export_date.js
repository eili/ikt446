db.export.find().forEach(function(doc) {
    db.export.update({_id: doc._id}, {
        $set: { 
            "date": doc.year + "-" + doc.month 
        }     
    })
})
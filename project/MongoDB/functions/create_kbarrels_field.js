db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "kbarrels": 1000 * doc.amount / (doc.usd * doc.value)
        }     
    })
})
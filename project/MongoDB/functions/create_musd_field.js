db.fact.find().forEach(function(doc) {
    db.fact.update({_id: doc._id}, {
        $set: { 
            "amountMusd": doc.amount / doc.usd
        }     
    })
})
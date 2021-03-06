//
//  User.swift
//  groSCery
//
//  Created by Naman Kedia on 10/28/18.
//  Copyright © 2018 Kristof Osswald. All rights reserved.
//

import Foundation

class User: Codable {
    var email: String
    var name: String
    var groupID: Int?
    
    init(email: String, name: String, groupID: Int?) {
        self.email = email
        self.name = name
        self.groupID = groupID
    }
    
    
}
